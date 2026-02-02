// Exponer tu función al objeto global
window.xDataLayout = function () {
    const asideElement = document.querySelector(".contenedor_aside");

    const seleccionadoNivel1 = parseInt(
        asideElement?.getAttribute("data-seleccionado-nivel-1")
    );
    const seleccionadoNivel2 = parseInt(
        asideElement?.getAttribute("data-seleccionado-nivel-2")
    );
    const seleccionadoNivel3 = parseInt(
        asideElement?.getAttribute("data-seleccionado-nivel-3")
    );
    const seleccionadoNivel4 = parseInt(
        asideElement?.getAttribute("data-seleccionado-nivel-4")
    );

    return {
        estadoAsideAbierto: false,
        estadoNavAbierto: false,
        seleccionadoNivel_1: !isNaN(seleccionadoNivel1)
            ? seleccionadoNivel1
            : null,
        seleccionadoNivel_2: !isNaN(seleccionadoNivel2)
            ? seleccionadoNivel2
            : null,
        seleccionadoNivel_3: !isNaN(seleccionadoNivel3)
            ? seleccionadoNivel3
            : null,
        seleccionadoNivel_4: !isNaN(seleccionadoNivel4)
            ? seleccionadoNivel4
            : null,

        initLayout() {
            let anchoPantalla = window.innerWidth || screen.width;

            if (anchoPantalla < 768) {
                this.estadoAsideAbierto = false;
                this.estadoNavAbierto = false;
            } else if (this.seleccionadoNivel_1) {
                this.estadoNavAbierto = true;
            }

            window.addEventListener("resize", () => {
                this.estadoAsideAbierto = document.body.clientWidth >= 768;
            });
        },

        toggleContenedorAside() {
            let anchoPantalla = window.innerWidth || screen.width;

            if (anchoPantalla < 768) {
                this.estadoAsideAbierto = true;
                if (this.seleccionadoNivel_1) this.estadoNavAbierto = true;
            }
        },

        toggleContenedorNavLinks() {
            let anchoPantalla = window.innerWidth || screen.width;

            if (anchoPantalla > 768 && this.seleccionadoNivel_1) {
                this.estadoNavAbierto = !this.estadoNavAbierto;
            } else if (anchoPantalla < 768) {
                this.estadoAsideAbierto = false;
            }
        },

        toogleNivel_1(event, id) {
            if (this.seleccionadoNivel_1 !== id) {
                this.estadoNavAbierto = true;
                this.seleccionadoNivel_1 = id;
                this.seleccionadoNivel_2 =
                    this.seleccionadoNivel_3 =
                    this.seleccionadoNivel_4 =
                        null;
            }
        },

        toogleNivel_2(event, id) {
            this.seleccionadoNivel_2 =
                this.seleccionadoNivel_2 === id ? null : id;
            this.seleccionadoNivel_3 = this.seleccionadoNivel_4 = null;
        },

        toogleNivel_3(event, id) {
            this.seleccionadoNivel_3 =
                this.seleccionadoNivel_3 === id ? null : id;
            this.seleccionadoNivel_4 = null;
        },

        toogleNivel_4(event, id) {
            this.seleccionadoNivel_4 =
                this.seleccionadoNivel_4 === id ? null : id;
        },

        resetMenu() {
            this.seleccionadoNivel_1 =
                this.seleccionadoNivel_2 =
                this.seleccionadoNivel_3 =
                this.seleccionadoNivel_4 =
                    null;
        },
    };
};
