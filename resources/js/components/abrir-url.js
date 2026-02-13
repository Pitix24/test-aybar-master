Livewire.on("abrirUrlLivewire", (url) => {
    if (!url) return;

    window.open(url, "_blank");
});
