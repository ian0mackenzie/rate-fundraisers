#!/bin/bash
# deploy.sh - Deployment script for AWS

set -e

PROJECT_NAME="rate-fundraisers"
AWS_REGION="us-east-1"
ECR_REPOSITORY="${PROJECT_NAME}"

echo "🚀 Deploying ${PROJECT_NAME} to AWS..."

# Check if AWS CLI is installed
if ! command -v aws &> /dev/null; then
    echo "❌ AWS CLI is not installed. Please install it first."
    exit 1
fi

# Check if Terraform is installed
if ! command -v terraform &> /dev/null; then
    echo "❌ Terraform is not installed. Please install it first."
    exit 1
fi

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install it first."
    exit 1
fi

# Get AWS Account ID
AWS_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
if [ -z "$AWS_ACCOUNT_ID" ]; then
    echo "❌ Could not get AWS Account ID. Please check your AWS credentials."
    exit 1
fi

echo "📋 AWS Account ID: $AWS_ACCOUNT_ID"
echo "🌍 Region: $AWS_REGION"

# Create ECR repository if it doesn't exist
echo "🏗️  Setting up ECR repository..."
aws ecr describe-repositories --repository-names $ECR_REPOSITORY --region $AWS_REGION 2>/dev/null || \
aws ecr create-repository --repository-name $ECR_REPOSITORY --region $AWS_REGION

# Get ECR login token
echo "🔐 Logging into ECR..."
aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com

# Build Docker image
echo "🐳 Building Docker image..."
docker build -f Dockerfile.prod -t $PROJECT_NAME:latest .

# Tag image for ECR
ECR_URI="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY:latest"
docker tag $PROJECT_NAME:latest $ECR_URI

# Push image to ECR
echo "📤 Pushing image to ECR..."
docker push $ECR_URI

# Deploy infrastructure with Terraform
echo "🏗️  Deploying infrastructure with Terraform..."
cd terraform

# Initialize Terraform
terraform init

# Check if tfvars file exists
if [ ! -f terraform.tfvars ]; then
    echo "❌ terraform.tfvars file not found!"
    echo "Please copy terraform.tfvars.example to terraform.tfvars and update the values."
    exit 1
fi

# Plan deployment
terraform plan

# Ask for confirmation
read -p "🤔 Do you want to proceed with the deployment? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Deployment cancelled."
    exit 1
fi

# Apply Terraform
terraform apply -auto-approve

# Get outputs
ALB_DNS=$(terraform output -raw alb_dns_name)
ECS_CLUSTER=$(terraform output -raw ecs_cluster_name)
ECS_SERVICE=$(terraform output -raw ecs_service_name)

cd ..

# Update ECS service with new image
echo "🔄 Updating ECS service with new image..."
aws ecs update-service \
    --cluster $ECS_CLUSTER \
    --service $ECS_SERVICE \
    --task-definition $(aws ecs describe-services --cluster $ECS_CLUSTER --services $ECS_SERVICE --query 'services[0].taskDefinition' --output text | sed 's/:.*$//')  \
    --region $AWS_REGION

# Wait for deployment to complete
echo "⏳ Waiting for deployment to complete..."
aws ecs wait services-stable --cluster $ECS_CLUSTER --services $ECS_SERVICE --region $AWS_REGION

echo "✅ Deployment completed successfully!"
echo "🌐 Application URL: http://$ALB_DNS"
echo "📊 Stats URL (with bug): http://$ALB_DNS/stats"
echo "💥 Guaranteed error: http://$ALB_DNS/stats/trigger-error"

# Set up database
echo "🗃️  Setting up database..."
echo "Note: You may need to manually run the database setup once the RDS instance is ready."
echo "Database endpoint will be shown in Terraform outputs."