#!/bin/bash
# setup-aws.sh - AWS CLI setup helper

echo "🔧 AWS CLI Setup Helper"

# Check if AWS CLI is installed
if command -v aws &> /dev/null; then
    echo "✅ AWS CLI is installed"
    
    # Check if configured
    if aws sts get-caller-identity &> /dev/null; then
        echo "✅ AWS CLI is configured"
        echo "📋 Current AWS Account:"
        aws sts get-caller-identity --output table
    else
        echo "⚠️  AWS CLI installed but not configured"
        echo "Run: aws configure"
        echo "You'll need:"
        echo "  • Access Key ID"
        echo "  • Secret Access Key"
        echo "  • Default region (us-east-1)"
        echo "  • Output format (json)"
    fi
else
    echo "❌ AWS CLI not installed"
    echo ""
    echo "📥 Install options:"
    echo "1. Homebrew: brew install awscli"
    echo "2. Direct download: https://aws.amazon.com/cli/"
    echo ""
    echo "🔑 After install, configure with: aws configure"
fi