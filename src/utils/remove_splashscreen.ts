
export default function removeSplashScreen(document: Document) {
    setTimeout(() => {
        document.getElementById('splash_screen').classList.add('remove');
    }, 1000);
    setTimeout(() => {
        let splash_screen = document.getElementById('splash_screen');
        let splash_screen_style = document.getElementById('splash_screen_style');
        splash_screen.parentElement.removeChild(splash_screen);
        splash_screen_style.parentElement.removeChild(splash_screen_style);
    }, 4000);
}
