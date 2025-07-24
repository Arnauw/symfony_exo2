# If script is running from a file, use its folder; otherwise use current dir
$base = $PSScriptRoot
if (-not $base) { $base = Get-Location }

$out = $base
mkdir $out -ea 0

Get-ChildItem $base -Filter *.docx |
  ForEach-Object {
    $src = $_.FullName
    $dst = Join-Path $out ($_.BaseName + ".pdf")
    docx2pdf $src $dst
  }
