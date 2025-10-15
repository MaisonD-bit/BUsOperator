Param()

Write-Host "Creating local .env from .env.example..."

$example = Join-Path -Path (Get-Location) -ChildPath ".env.example"
$dest = Join-Path -Path (Get-Location) -ChildPath ".env"

if (-not (Test-Path $example)) {
    Write-Error ".env.example not found in repository root. Run this script from the Laravel project root."
    exit 1
}

if (Test-Path $dest) {
    $ov = Read-Host ".env already exists. Overwrite? (y/N)"
    if ($ov -ne 'y' -and $ov -ne 'Y') { Write-Host "Aborted."; exit 0 }
}

Copy-Item -Path $example -Destination $dest -Force

Write-Host "Please enter PayMaya keys for local development (they will be written to .env only)."
$public = Read-Host -Prompt "PAYMAYA_PUBLIC_KEY (paste or press Enter to skip)"
$secret = Read-Host -Prompt "PAYMAYA_SECRET_KEY (paste or press Enter to skip)"
$base = Read-Host -Prompt "PAYMAYA_BASE_URL (default: https://pg-sandbox.paymaya.com)"

if (-not $base) { $base = 'https://pg-sandbox.paymaya.com' }

if ($public -or $secret) {
    (Get-Content $dest) | ForEach-Object {
        $_ -replace '^PAYMAYA_PUBLIC_KEY=.*', "PAYMAYA_PUBLIC_KEY=$public" -replace '^PAYMAYA_SECRET_KEY=.*', "PAYMAYA_SECRET_KEY=$secret" -replace '^PAYMAYA_BASE_URL=.*', "PAYMAYA_BASE_URL=$base"
    } | Set-Content $dest
    Write-Host ".env created and PayMaya keys inserted (local only)."
} else {
    Write-Host "No PayMaya keys entered. .env created from example without keys.";
}

Write-Host "Run 'php artisan key:generate' and restart your server if necessary."
