# Transfer FROM LOCAL DISK TO S3
A simple script to transfer files from local path to AWS S3

## HOW TO USE
After git pull, run 
```composer install```

Create `.env` in your project root with the following:
```
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=

LOCAL_SOURCE_PATH="./path/to/directory"
```

After completing above, run 
``` php script.php```

### Notes:
1. Transfer is done synchronously
2. Files are copied, original files are not deleted.


