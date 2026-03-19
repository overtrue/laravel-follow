# AGENTS.md

This repository is maintained with AI assistance (OpenClaw).

## Support policy

- Laravel: **13+ only**
- PHP: **8.3+**

## Local workflow

```bash
composer install
composer check-style
composer test
```

## Notes

- Tests run via Orchestra Testbench.
- Keep CI green before tagging.

## Release workflow

1. Update dependencies for Laravel 13.
2. Run `composer check-style` and `composer test`.
3. Commit and push.
4. Wait for GitHub Actions to pass.
5. Tag and publish with `gh release create`.
