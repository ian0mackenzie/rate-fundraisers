#!/bin/bash
# get-app-url.sh - Find your deployed app URL

echo "🔍 Finding your Rate Fundraisers app..."

# Check if in terraform directory
if [ ! -f "terraform/terraform.tfstate" ]; then
    echo "❌ No terraform state found. Have you deployed yet?"
    echo "Run: ./quick-deploy.sh"
    exit 1
fi

cd terraform

# Get ALB DNS name from Terraform outputs
ALB_DNS=$(terraform output -raw alb_dns_name 2>/dev/null)

if [ -z "$ALB_DNS" ]; then
    echo "❌ Could not get ALB DNS name from Terraform"
    echo "Try running: terraform refresh"
    exit 1
fi

echo "✅ Found your app!"
echo ""
echo "🌐 Main App: http://$ALB_DNS"
echo "📊 Stats (with bug): http://$ALB_DNS/stats"
echo "💥 Trigger error: http://$ALB_DNS/stats/trigger-error"
echo "❤️  Health check: http://$ALB_DNS/health"
echo ""

# Check if app is responding
echo "🏥 Checking app health..."
if curl -s "http://$ALB_DNS/health" > /dev/null; then
    echo "✅ App is healthy and responding!"
else
    echo "⚠️  App might still be starting up..."
    echo "Check ECS service status in AWS Console"
fi