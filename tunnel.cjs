const ngrok = require('C:/Users/LENOVO/AppData/Roaming/npm/node_modules/@ngrok/ngrok');

async function start() {
    try {
        console.log("Menghubungkan ke Ngrok...");
        const listener = await ngrok.forward({
            addr: 8000,
            authtoken: "3IEDxuhLb8w918fRQQ93cM6ZfD9_BmD7xhvkgduiXdvoD4qD",
            domain: "bolt-numerate-eskimo.ngrok-free.dev"
        });
        console.log(`TUNNEL_ACTIVE: ${listener.url()}`);

        // Keep process alive indefinitely
        setInterval(() => {}, 1000 * 60 * 60);
    } catch (err) {
        console.error("Ngrok error:", err);
        process.exit(1);
    }
}

start();
