# Aset SPMB Pro

Struktur:

```
assets/
├── src/                    — source CSS (Tailwind v4 entry)
│   ├── theme.css           — shared @theme design tokens
│   ├── spmb-public.css     — entry public form/tracking/announcement
│   └── spmb-admin.css      — entry admin panel
├── css/
│   ├── spmb-public.css     — fallback vanilla (sebelum build)
│   ├── spmb-admin.css      — fallback vanilla (sebelum build)
│   └── dist/               — output Tailwind compile (di-commit)
│       ├── spmb-public.css
│       └── spmb-admin.css
└── js/
```

## Build

Tailwind v4 CLI. Butuh Node 18+.

```bash
cd wp-content/plugins/spmb-pro
npm install
npm run build          # build public + admin
npm run dev           # watch public
npm run dev:admin     # watch admin
```

`npm run build` menghasilkan `assets/css/dist/spmb-*.css`. File ini di-commit ke repo.

Sebelum build pertama, plugin otomatis fallback ke `assets/css/spmb-*.css` (vanilla lama).

## Design tokens

Lihat `assets/src/theme.css` — palet Linear-like: Primary `#1E63E8`, Accent `#FFB703`, neutral slate. Radius 4/6/8px, motion 150ms ease-out custom curve.

## Catatan

- Preflight Tailwind dinonaktifkan — plugin WP, harus coexist dengan CSS WP & tema.
- Class `.spmb-*` tidak di-rename — dipakai di `views/`, `assets/js/`, dan tema `spmb-overrides.css`.
- Display rule `.spmb-submit`/`.spmb-next` (JS toggle `data-final-step`) tidak diubah.