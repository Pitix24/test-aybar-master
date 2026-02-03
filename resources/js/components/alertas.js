function alertaNormal(mensaje) {
    event.preventDefault();

    if (mensaje == "Creado") {
        Swal.fire({
            icon: "success",
            title: mensaje,
            showConfirmButton: false,
            timer: 2500,
        });
    } else if (mensaje == "Actualizado") {
        Swal.fire({
            icon: "success",
            title: mensaje,
            showConfirmButton: false,
            timer: 2500,
        });
    } else if (mensaje == "Eliminado") {
        Swal.fire({
            icon: "success",
            title: mensaje,
            showConfirmButton: false,
            timer: 2500,
        });
    } else {
    }
}

Livewire.on("alertaLivewire", (data) => {
    const payload = data[0];

    const successTitles = [
        "Creado",
        "Actualizado",
        "Agregado",
        "Importado",
        "Derivado",
        "Adjuntado",
        "Validado",
        "Enviado",
    ];
    const errorTitles = ["Error", "Eliminado", "Quitado"];
    const warningTitles = ["Advertencia"];

    let icon = "info";
    let timer = payload.timer ?? 2500;

    // 1. Icono y comportamiento por tipo
    if (successTitles.includes(payload.title)) {
        icon = "success";
    } else if (errorTitles.includes(payload.title)) {
        icon = "error";
    } else if (warningTitles.includes(payload.title)) {
        icon = "warning";
    }

    // 2. Confirm button (backend tiene prioridad)
    const showConfirmButton =
        payload.showConfirmButton !== undefined
            ? payload.showConfirmButton
            : errorTitles.includes(payload.title) ||
              warningTitles.includes(payload.title);

    // 3. Regla UX: si hay confirmación, no hay timer
    if (showConfirmButton === true) {
        timer = null;
    }

    Swal.fire({
        icon,
        title: payload.title ?? "",
        text: payload.text ?? "",
        showConfirmButton,
        timer,
    });
});

Livewire.on("abrirUrlLivewire", (url) => {
    if (!url) return;

    window.open(url, "_blank");
});
