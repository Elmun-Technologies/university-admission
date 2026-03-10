#!/bin/bash

# push_to_github.sh - Final step to push the project to GitHub
# Run this in your terminal to authenticate and push the code.

set -e

REPO_URL="https://github.com/Elmun-Technologies/university-admission.git"

echo "--- 📤 Ready to Push to GitHub ---"
echo "Repository: ${REPO_URL}"

# 1. Ensure remote is set
if git remote | grep -q 'origin'; then
    git remote set-url origin "${REPO_URL}"
else
    git remote add origin "${REPO_URL}"
fi

# 2. Push to main
echo "Pushing code to 'main' branch..."
echo "Note: This may ask for your GitHub username and Personal Access Token (PAT)."
git push -u origin main

echo "--- ✅ Push Successful! ---"
echo "View your code at: https://github.com/Elmun-Technologies/university-admission"
