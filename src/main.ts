import App from './App.svelte';

const app = new App({
    target: document.getElementById('app')
});

// Prevent ctrl+click from opening a new window
document.addEventListener('click', function(event: MouseEvent) {
    if (event.target instanceof HTMLElement && event.target.tagName.toLowerCase() === 'a' && event.ctrlKey) {
        event.preventDefault();
        return false
    }
});

// Prevent mousewheel click from opening a new windows
document.addEventListener('auxclick', function(event: MouseEvent) {
    if (event.target instanceof HTMLElement && event.target.tagName.toLowerCase() === 'a' &&  event.button === 1) {
        event.preventDefault();
        return false
    }
});

export default app;
