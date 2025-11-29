#!/bin/bash
# quick-deploy.sh - Single command to deploy the recommended ALB + Fargate setup

set -e

echo "🚀 Deploying Rate Fundraisers with ALB + Fargate..."

# Check prerequisites
if ! command -v aws &> /dev/null; then
    echo "❌ AWS CLI not found. Install it first: https://aws.amazon.com/cli/"
    exit 1
fi

if ! command -v terraform &> /dev/null; then
    echo "❌ Terraform not found. Install it first: https://terraform.io"
    exit 1
fi

if ! command -v docker &> /dev/null; then
    echo "❌ Docker not found. Install it first: https://docker.com"
    exit 1
fi

# Configuration
PROJECT_NAME="rate-fundraisers"
AWS_REGION="us-east-1"
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)

if [ -z "$AWS_ACCOUNT_ID" ]; then
    echo "❌ Could not get AWS Account ID. Check your AWS credentials."
    exit 1
fi

echo "📋 Deploying to AWS Account: $AWS_ACCOUNT_ID"
echo "🌍 Region: $AWS_REGION"

# 1. Create ECR repository
echo "🏗️ Setting up ECR repository..."
aws ecr describe-repositories --repository-names $PROJECT_NAME --region $AWS_REGION 2>/dev/null || \
aws ecr create-repository --repository-name $PROJECT_NAME --region $AWS_REGION

# 2. Build and push Docker image
echo "🐳 Building Docker image..."
aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com

docker build -f Dockerfile.prod -t $PROJECT_NAME:latest .
ECR_URI="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$PROJECT_NAME:latest"
docker tag $PROJECT_NAME:latest $ECR_URI
docker push $ECR_URI

# 3. Deploy infrastructure
echo "🏗️ Deploying infrastructure..."
cd terraform

# Create tfvars if it doesn't exist
if [ ! -f terraform.tfvars ]; then
    echo "📝 Creating terraform.tfvars..."
    cat > terraform.tfvars << EOF
# Basic configuration
aws_region = "$AWS_REGION"
project_name = "$PROJECT_NAME"
environment = "production"

# Database configuration
db_username = "root"
db_password = "SecurePass123!"  # Change this for production!
db_name = "rate_fundraisers"

# Application configuration
app_port = 80
EOF
fi

# Initialize and deploy
terraform init
echo "📋 Terraform plan:"
terraform plan

read -p "🤔 Deploy this infrastructure? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Deployment cancelled."
    exit 1
fi

terraform apply -auto-approve

# 4. Update ECS service with new image
echo "🔄 Updating ECS service..."
CLUSTER_NAME=$(terraform output -raw ecs_cluster_name)
SERVICE_NAME=$(terraform output -raw ecs_service_name)
ALB_DNS=$(terraform output -raw alb_dns_name)

# Update the task definition with the correct image
TASK_DEF_ARN=$(aws ecs describe-services \
    --cluster $CLUSTER_NAME \
    --services $SERVICE_NAME \
    --query 'services[0].taskDefinition' \
    --output text)

# Get current task definition
TASK_DEF=$(aws ecs describe-task-definition --task-definition $TASK_DEF_ARN)

# Update container image in task definition
NEW_TASK_DEF=$(echo $TASK_DEF | jq --arg IMAGE "$ECR_URI" '
    .taskDefinition | 
    del(.taskDefinitionArn, .revision, .status, .requiresAttributes, .placementConstraints, .compatibilities, .registeredAt, .registeredBy) |
    .containerDefinitions[0].image = $IMAGE
')

# Register new task definition
NEW_TASK_DEF_ARN=$(echo $NEW_TASK_DEF | aws ecs register-task-definition --cli-input-json file:///dev/stdin --query 'taskDefinition.taskDefinitionArn' --output text)

# Update service
aws ecs update-service \
    --cluster $CLUSTER_NAME \
    --service $SERVICE_NAME \
    --task-definition $NEW_TASK_DEF_ARN

echo "⏳ Waiting for deployment to complete..."
aws ecs wait services-stable --cluster $CLUSTER_NAME --services $SERVICE_NAME

echo ""
echo "🎉 Deployment Complete!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Application URL: http://$ALB_DNS"
echo "📊 Stats (with bug): http://$ALB_DNS/stats"
echo "💥 Guaranteed crash: http://$ALB_DNS/stats/trigger-error"
echo "❤️  Health check: http://$ALB_DNS/health"
echo ""
echo "🔧 Setup Details:"
echo "   • Load Balancer: ALB (handles SSL when you add a domain)"
echo "   • Container: Fargate (serverless, Apache + PHP)"
echo "   • Database: RDS MySQL"
echo "   • Logs: CloudWatch (/ecs/$PROJECT_NAME)"
echo ""
echo "🎯 Ready for Sentry integration!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"