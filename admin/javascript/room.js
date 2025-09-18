// admin/js/room.js

document.addEventListener('DOMContentLoaded', () => {
  // Delegación: todos los formularios de estado
  document.querySelectorAll('.js-status-form').forEach((form) => {
    const roomId = form.getAttribute('data-room');

    // Escuchamos clicks en los botones de estado
    form.querySelectorAll('button[name="status"]').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault(); // no submit tradicional

        const status = btn.value;
        const data = new URLSearchParams();
        data.append('change_status', '1');
        data.append('room_id', roomId);
        data.append('status', status);

        fetch('room.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: data.toString()
        })
        .then(async (res) => {
          const isJson = res.headers.get('content-type')?.includes('application/json');
          const payload = isJson ? await res.json() : { ok: res.ok };
          if (!res.ok || !payload.ok) {
            throw new Error(payload?.error || 'Error al actualizar estado');
          }

          // Actualizar badge visualmente
          const badge = document.querySelector(`#room-${roomId} .badge`);
          if (badge) {
            const classMap = {
              'Disponible': 'bg-success text-white',
              'Reservada': 'bg-warning text-dark',
              'Limpieza': 'bg-info text-dark',
              'Ocupada': 'bg-danger text-white'
            };
            badge.textContent = status;
            badge.className = 'badge ' + (classMap[status] || 'bg-secondary text-white');
          }
        })
        .catch((err) => {
          console.error(err);
          // feedback mínimo (opcional)
          alert('No se pudo actualizar el estado. Reintenta.');
        });
      });
    });
  });
});
