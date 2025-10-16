# R2 Storage Tests

This directory contains tests for Cloudflare R2 integration to ensure proper file storage functionality.

## Test Types

### Unit Tests (`tests/Unit/Storage/R2ConfigurationTest.php`)

-   **Fast execution** - Tests configuration without network calls
-   **Always runs** - Part of the standard test suite
-   **Validates**: Configuration structure, environment variable mapping, disk setup

```bash
# Run configuration tests only
php artisan test tests/Unit/Storage/R2ConfigurationTest.php
```

### Integration Tests (`tests/Feature/Storage/R2IntegrationTest.php`)

-   **Requires R2 credentials** - Tests actual R2 connectivity
-   **Network dependent** - Makes real HTTP requests to R2
-   **Validates**: File operations, URL generation, complete workflow

```bash
# Run all R2 integration tests
php artisan test tests/Feature/Storage/R2IntegrationTest.php

# Run specific test groups
php artisan test --group=integration,r2
php artisan test --group=workflow
php artisan test --group=slow
```

## Test Groups

-   `integration` - Tests that require R2 connectivity
-   `r2` - All R2-related tests
-   `workflow` - End-to-end background removal workflow tests
-   `slow` - Tests that may take longer (HTTP requests)

## What's Tested

### ✅ Configuration Validation

-   R2 disk configuration exists and is properly structured
-   Environment variables are correctly mapped
-   Default filesystem disk is set to R2
-   Required configuration keys are present

### ✅ Connectivity

-   Can connect to R2 and list bucket contents
-   Can perform basic operations without errors
-   Storage disk instance can be created

### ✅ File Operations

-   Upload and download text files
-   Upload and download image files
-   File existence checks
-   File size verification
-   File cleanup/deletion

### ✅ URL Generation

-   Can generate public URLs for stored files
-   Generated URLs are valid HTTPS URLs
-   URLs are accessible over HTTP
-   URLs contain the correct file path

### ✅ Background Removal Workflow

-   Complete file upload/processing/download cycle
-   Original and processed file handling
-   URL generation for both file types
-   Proper file cleanup

## Environment Requirements

For integration tests to run, the following environment variables must be set in `.env`:

```env
FILESYSTEM_DISK=r2
CLOUDFLARE_R2_ACCESS_KEY_ID=your_access_key
CLOUDFLARE_R2_SECRET_ACCESS_KEY=your_secret_key
CLOUDFLARE_R2_BUCKET=your_bucket_name
CLOUDFLARE_R2_ENDPOINT=https://your_account_id.r2.cloudflarestorage.com
CLOUDFLARE_R2_URL=https://your_public_url.r2.dev
CLOUDFLARE_R2_USE_PATH_STYLE_ENDPOINT=false
```

## Test Skipping

Integration tests automatically skip if R2 credentials are not configured, preventing failures in environments where R2 is not set up.

## Running in CI/CD

For continuous integration, you can:

1. **Run unit tests only** (fast, no external dependencies):

    ```bash
    php artisan test tests/Unit/Storage/
    ```

2. **Run integration tests in environments with R2 credentials**:

    ```bash
    php artisan test tests/Feature/Storage/ --group=integration
    ```

3. **Skip integration tests** if R2 is not available:
    ```bash
    php artisan test --exclude-group=integration,r2
    ```

## Troubleshooting

If integration tests fail:

1. **Check R2 credentials** - Ensure all environment variables are set correctly
2. **Verify R2 bucket permissions** - Ensure read/write access is configured
3. **Check network connectivity** - Ensure your environment can reach Cloudflare R2
4. **Review bucket CORS settings** - If URL access fails, check CORS configuration
5. **Validate bucket public access** - Ensure public URLs are enabled for your bucket
