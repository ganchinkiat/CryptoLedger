#!/bin/bash
cd /var/www/html
if [ -z "$(git status --porcelain)" ]; then
  echo "No changes to commit"
else
  git add -A
  git commit -m "Consolidate migrations; update model and import script; re-import data"
  git push
fi
