<script context="module" lang="ts">
	// Modal from https://svelte.dev/repl/514f1335749a4eae9d34ad74dc277f20

	const modals = {};

	export function getModal(id: string = ''): { open: Function; close: Function } {
		return modals[id];
	}
</script>

<script lang="ts">
	import { onDestroy } from 'svelte';

	export let clickAction: Function = null;
	export let title: string = '';

	let show: boolean = false;

	let currentModal: HTMLElement;

	let closeCallback: Function;
	let bodyOverflow: string = 'initial';

	export let id = '';

	function escapeKeyListener(ev) {
		if (!show) {
			return;
		}
		// only respond if the current modal is the top one
		if (ev.key == 'Escape') {
			close();
		}
	}

	function open(callback: Function = null) {
		closeCallback = callback;
		if (show) {
			return;
		}

		bodyOverflow = 'hidden';

		show = true;

		document.body.appendChild(currentModal);
	}

	function close(returnValue: any = null) {
		if (!show) {
			return;
		}

		bodyOverflow = 'initial';

		show = false;

		if (closeCallback) {
			closeCallback(returnValue);
		}
	}

	modals[id] = { open, close };

	onDestroy(() => {
		delete modals[id];
	});

	// Forced to use this concatenation to avoid postcss to interpret this string as a real style tag...
	$: bodyStyle = '<' + `style id="modal_style">body { overflow: ${bodyOverflow}; }</style>`;
</script>

<svelte:head>
	{@html bodyStyle}
</svelte:head>

<svelte:window on:keydown={escapeKeyListener} />

<div bind:this={currentModal} class:show class="modal fade" tabindex="-1" on:click={() => close()}>
	<div class="modal-dialog" on:click|stopPropagation={() => {}}>
		<div class="modal-content">
			<div class="modal-header">
				{#if title}
					<h5 class="modal-title">{title}</h5>
				{/if}
				<button
					type="button"
					class="btn-close"
					data-bs-dismiss="modal"
					aria-label="Close"
					on:click={() => close()}
				/>
			</div>
			<div class="modal-body">
				<slot />
			</div>
			<div class="modal-footer">
				<button
					type="button"
					class="btn btn-secondary"
					data-bs-dismiss="modal"
					on:click={() => close()}>Close</button
				>
				{#if clickAction}
					<button type="button" class="btn btn-primary" on:click={clickAction}>Done</button>
				{/if}
			</div>
		</div>
	</div>
</div>

<style>
	.show {
		display: block;
	}
	.modal {
		background-color: rgba(0, 0, 0, 0.5);
	}
</style>
