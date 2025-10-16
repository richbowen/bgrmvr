<?php

declare(strict_types=1);

use Tests\TestCase;

class R2ConfigurationTest extends TestCase
{
    public function test_has_r2_disk_configuration_defined(): void
    {
        $disks = config('filesystems.disks');

        $this->assertArrayHasKey('r2', $disks);
    }

    public function test_r2_disk_uses_s3_driver(): void
    {
        $r2Config = config('filesystems.disks.r2');

        $this->assertEquals('s3', $r2Config['driver']);
    }

    public function test_r2_disk_has_required_configuration_keys(): void
    {
        $r2Config = config('filesystems.disks.r2');

        $requiredKeys = ['driver', 'key', 'secret', 'region', 'bucket', 'endpoint', 'visibility'];

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $r2Config, "R2 configuration missing required key: {$key}");
        }
    }

    public function test_r2_disk_has_public_visibility_for_url_generation(): void
    {
        $r2Config = config('filesystems.disks.r2');

        $this->assertEquals('public', $r2Config['visibility']);
    }

    public function test_default_filesystem_disk_is_set_to_r2(): void
    {
        $this->assertEquals('r2', config('filesystems.default'));
    }

    public function test_environment_variables_are_properly_mapped(): void
    {
        // Test that the configuration properly references environment variables
        $r2Config = config('filesystems.disks.r2');

        // These should be set to actual values in production, but we're testing the structure
        $this->assertEquals(env('CLOUDFLARE_R2_ACCESS_KEY_ID'), $r2Config['key']);
        $this->assertEquals(env('CLOUDFLARE_R2_SECRET_ACCESS_KEY'), $r2Config['secret']);
        $this->assertEquals(env('CLOUDFLARE_R2_BUCKET'), $r2Config['bucket']);
        $this->assertEquals(env('CLOUDFLARE_R2_ENDPOINT'), $r2Config['endpoint']);
        $this->assertEquals(env('CLOUDFLARE_R2_URL'), $r2Config['url']);
    }
}
