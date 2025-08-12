window.jsPDF = window.jspdf.jsPDF;

document.getElementById('reporteForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const btnDescargar = document.querySelector('.btn-descargar-pdf');
    const btnVolver = document.querySelector('.btn-volver');
    btnDescargar.style.display = 'none';
    btnVolver.style.display = 'none';

    try {
        const doc = new jsPDF('p', 'pt', 'a4');
        const content = document.querySelector('.card-container');
        const formData = new FormData(this);

        // 1. Capturar la primera página
        await html2canvas(content, { scale: 3 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgProps = doc.getImageProperties(imgData);
            const pdfWidth = doc.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
            const margin = 20;

            doc.addImage(imgData, 'PNG', margin, margin, pdfWidth - 2 * margin, pdfHeight - 2 * margin);
        });

        // 2. Consultar API para generar la segunda página, enviando los datos del formulario
        try {
            const response = await fetch('reporte_api.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error("Server Error:", errorText);
                throw new Error(`Server responded with an error: ${response.status}`);
            }

            const data = await response.json();

            // 3. Generar la segunda página del PDF con los datos de la API
            doc.addPage();
            let y = 40;

            doc.setFontSize(18);
            doc.text("Resumen de Datos", 40, y);
            y += 30;

            doc.setFontSize(12);
            doc.text(`Fecha del Reporte: ${data.fecha}`, 40, y); y += 20;
            doc.text(`Hora: ${data.hora}`, 40, y); y += 20;
            doc.text(`Asesor con más ventas: ${data.asesor_mas_ventas} ($${data.monto_asesor_ventas.toFixed(2)})`, 40, y); y += 20;
            doc.text(`Devoluciones / Cambios: ${data.devoluciones_cambios}`, 40, y); y += 20;
            doc.text(`Número de visitas a la tienda: ${data.num_visitas}`, 40, y); y += 20;

            const comentariosTexto = data.comentarios || '';
            const comentariosArray = doc.splitTextToSize(comentariosTexto, doc.internal.pageSize.getWidth() - 80);
            doc.text("Comentarios / Incidencias:", 40, y);
            y += 20;
            doc.text(comentariosArray, 60, y);
            y += comentariosArray.length * 15;
            y += 15;

            doc.setFontSize(14);
            doc.text("Productos más vendidos:", 40, y); y += 20;

            doc.setFontSize(12);
            if (data.productos_mas_vendidos && data.productos_mas_vendidos.length > 0) {
                data.productos_mas_vendidos.forEach((producto, index) => {
                    doc.text(`${index + 1}. ${producto}`, 60, y);
                    y += 20;
                });
            } else {
                doc.text("No hay productos más vendidos registrados hoy.", 60, y);
                y += 20;
            }

        } catch (error) {
            // **Este es el bloque que se ejecuta si la llamada a la API falla**
            doc.addPage();
            doc.setFontSize(14);
            doc.setTextColor('#FFFFFF');
            doc.text("Error al cargar datos adicionales del reporte desde la API.", 40, 60);
            doc.setTextColor('#000000');
            console.error("Error fetching data for the report:", error);
        }

        doc.save('reporte_diario.pdf');

    } catch (error) {
        console.error("Error in the PDF generation process:", error);
        alert('Ocurrió un error inesperado. Por favor, revisa la consola para más detalles.');
    } finally {
        btnDescargar.style.display = 'block';
        btnVolver.style.display = 'block';
    }
});