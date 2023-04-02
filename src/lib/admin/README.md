Svelte admin, prototype
=======================

⚠ This package is totally a **prototype**. It's used in one single personal project and not meant for production (yet)!

## Install

Install `@orbitale/svelte-admin` via `yarn` or `npm`:

```bash
yarn add -D "@orbitale/svelte-admin@git://github.com/Orbitale/SvelteAdmin.git"
npm install -D "@orbitale/svelte-admin@git://github.com/Orbitale/SvelteAdmin.git"
```

## Usage

In a Svelte route file or component, use the `PaginatedTable` component to initialize an admin list:

```sveltehtml
<script lang="ts">
	import PaginatedTable from '@orbitale/svelte-admin/components/PaginatedTable/PaginatedTable.svelte';
	import ActionParams from '@orbitale/svelte-admin/src/ActionParams';
	import Field from '@orbitale/svelte-admin/src/Field';
	import UrlAction from '@orbitale/svelte-admin/src/UrlAction';
    
    type Page = {
        id: number,
        title: string,
        content: string,
    };
    
    function getPages(): Page[] {
        return [
            {id: 1, title: 'First page', content: 'Lorem ipsum'},
            {id: 2, title: 'Second page', content: 'dolor sit amet'},
        ];
    }

    const pagesStore: Writable<Page[]> = writable(getPages());

    let pages: Page[] = [];

	let fields = [
        new Field('id', 'ID'), 
        new Field('title', 'Title'),
        new Field('content', 'Content'),
    ];

	let actions = [
        new UrlAction('Edit', '/pages/edit/:id', ActionParams.id()),
    ];
</script>

<h1>Pages</h1>

<PaginatedTable id="pages" items_store={pagesStore} {fields} {actions} />
```
