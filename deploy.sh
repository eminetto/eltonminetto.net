BUCKET_NAME=eltonminetto.net

# Build a fresh copy
hugo

# Copy over pages - not static js/img/css/downloads
aws s3 sync --acl "public-read" public/ s3://$BUCKET_NAME/ --exclude 'img' --exclude 'js' --exclude 'css' --exclude 'post'

# Ensure static files are set to cache forever - cache for a month --cache-control "max-age=2592000"
aws s3 sync --cache-control "max-age=2592000" --acl "public-read" public/img/ s3://$BUCKET_NAME/img/
aws s3 sync --cache-control "max-age=2592000" --acl "public-read" public/css/ s3://$BUCKET_NAME/css/
aws s3 sync --cache-control "max-age=2592000" --acl "public-read" public/js/ s3://$BUCKET_NAME/js/
