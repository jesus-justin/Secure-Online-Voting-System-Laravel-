# PWA App Icons

This folder should contain the following app icons for Progressive Web App functionality:

## Required Icons

1. **icon-72x72.png** - 72 x 72 pixels
2. **icon-96x96.png** - 96 x 96 pixels
3. **icon-128x128.png** - 128 x 128 pixels
4. **icon-144x144.png** - 144 x 144 pixels
5. **icon-152x152.png** - 152 x 152 pixels
6. **icon-192x192.png** - 192 x 192 pixels
7. **icon-384x384.png** - 384 x 384 pixels
8. **icon-512x512.png** - 512 x 512 pixels

## How to Create Icons

You can create these icons from a single high-resolution image (at least 512x512 pixels) using:

### Option 1: Online Tools
- [PWA Asset Generator](https://www.pwabuilder.com/)
- [Real Favicon Generator](https://realfavicongenerator.net/)

### Option 2: Using ImageMagick (Command Line)
```bash
convert logo.png -resize 72x72 icon-72x72.png
convert logo.png -resize 96x96 icon-96x96.png
convert logo.png -resize 128x128 icon-128x128.png
convert logo.png -resize 144x144 icon-144x144.png
convert logo.png -resize 152x152 icon-152x152.png
convert logo.png -resize 192x192 icon-192x192.png
convert logo.png -resize 384x384 icon-384x384.png
convert logo.png -resize 512x512 icon-512x512.png
```

### Option 3: Using Photoshop/GIMP
1. Open your source logo/image
2. Resize canvas to each required size
3. Export as PNG
4. Save with the naming convention above

## Design Guidelines

- Use a simple, recognizable design
- Ensure the icon works on both light and dark backgrounds
- Avoid text that's too small (won't be readable at smaller sizes)
- Use the brand colors of the voting system
- Recommended: Use a transparent background or solid color background
- Include padding around the main icon (safe area: 10% margin)

## Current Status

⚠️ **Icons not yet created** - Please add the required icon files to this directory.

Once icons are created, the PWA functionality will be fully operational.
