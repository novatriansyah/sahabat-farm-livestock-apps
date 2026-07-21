$ErrorActionPreference = "Stop"
function Write-PHPFile {
    param($Path, $Content)
    $dir = Split-Path $Path -Parent
    if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Force -Path $dir | Out-Null }
    Set-Content -Path $Path -Value $Content -Encoding UTF8
    Write-Host "Created: $Path"
}