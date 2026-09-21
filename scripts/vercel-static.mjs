import { cp, mkdir } from 'node:fs/promises';
import { basename } from 'node:path';

// The public PHP entrypoint must never be published as a static source file.
await mkdir('dist', { recursive: true });
await cp('public', 'dist', {
    recursive: true,
    filter: (source) => !basename(source).startsWith('.')
        && !/\.(php|phtml|ini)$/i.test(source)
        && basename(source) !== 'hot',
});
