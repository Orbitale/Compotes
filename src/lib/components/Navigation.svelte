<script lang="ts">
	import OperationsSynchronizer from '$lib/struct/OperationsSynchronizer';
	import SpinLoader from '$lib/admin/components/SpinLoader.svelte';
	import { page } from '$app/stores';
	import { ToastType } from '$lib/struct/Toast';
	import message from '$lib/utils/message';

	let syncing: boolean = false;

	const links = [
		{ url: '/analytics', label: 'Analytics' },
		{ url: '/operations', label: 'Operations' },
		{ url: '/bank-accounts', label: 'Bank accounts' },
		{ url: '/triage', label: 'Triage' },
		{ url: '/tag-rules', label: 'Tag rules' },
		{ url: '/tags', label: 'Tags' },
		{ url: '/import', label: 'Import' }
	];

	async function sync() {
		syncing = true;
		const res = await OperationsSynchronizer.sync();

		console.info({res});
		message(res.isOk() ? res.unwrap() : res.unwrapErr(), res.isOk() ? ToastType.success : ToastType.error);
		syncing = false;
	}
</script>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<div class="container-fluid">
		<a class="navbar-brand" href="/">Home</a>

		<button
			class="navbar-toggler"
			type="button"
			data-bs-toggle="collapse"
			data-bs-target="#navbarNav"
			aria-controls="navbarNav"
			aria-expanded="false"
			aria-label="Toggle navigation"
		>
			<span class="navbar-toggler-icon" />
		</button>

		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav nav-fill">
				{#each links as link}
					<li class="nav-item">
						<a href={link.url} class="nav-link" class:current={$page.url.pathname === link.url}>
							{link.label}
						</a>
					</li>
				{/each}

				<li class="nav-item" class:syncing>
					<button class="nav-link" on:click={sync}>
						Sync
						<SpinLoader display={syncing} />
					</button>
				</li>
			</ul>
		</div>
	</div>
</nav>

<style lang="scss">
	@-webkit-keyframes rotating /* Safari and Chrome */ {
		from {
			transform: rotate(0deg);
		}
		to {
			transform: rotate(360deg);
		}
	}
	@keyframes rotating {
		from {
			transform: rotate(0deg);
		}
		to {
			transform: rotate(360deg);
		}
	}

	.syncing.nav-item {
		&,
		* {
			cursor: not-allowed;
		}

		.nav-link {
			pointer-events: none;
			color: #ddd;
		}
	}

	.nav-link {
		border: solid 1px transparent;
		border-radius: 8px;
		color: rgba(0, 0, 0, 0.4);
		&:hover {
			background: #eaeaea;
		}
		&:active {
			border-color: #b0b0b0;
			background: #d6d6d6;
		}
		&.current {
			color: rgba(0, 0, 0, 0.8);
			border-color: #fcfcfc;
			background: #eee;
			&:hover {
				border-color: #fcfcfc;
				background: #eee;
			}
		}
	}
</style>
