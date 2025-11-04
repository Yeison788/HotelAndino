<?php
declare(strict_types=1);

/**
 * Chatbot especializado del Hotel Andino.
 * Gestiona respuestas frecuentes y reserva de habitaciones sin depender de APIs externas.
 */
class HotelChatbotResponder
{
    private const SESSION_KEY = 'chatbot_booking';

    private mysqli $conn;
    private ?int $userId;
    private ?array $activeReservation;

    /** @var array<string, mixed> */
    private array $knowledge;

    /** @var array<string, string> */
    private array $roomTypes;

    /** @var array<string, string> */
    private array $bedOptions;

    /** @var array<string, string> */
    private array $mealPlans;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
        guest_portal_ensure_schema($conn);

        $this->roomTypes = [
            'habitacion doble'    => 'Habitación Doble',
            'doble'               => 'Habitación Doble',
            'habitacion suite'    => 'Habitación Suite',
            'suite'               => 'Habitación Suite',
            'habitacion multiple' => 'Habitación Múltiple',
            'multiple'            => 'Habitación Múltiple',
            'habitacion sencilla' => 'Habitación Sencilla',
            'sencilla'            => 'Habitación Sencilla',
        ];

        $this->bedOptions = [
            '1 cliente' => '1 cliente',
            'uno'       => '1 cliente',
            '2 clientes'=> '2 clientes',
            'dos'       => '2 clientes',
            '3 clientes'=> '3 clientes',
            'tres'      => '3 clientes',
            '4 clientes'=> '4 clientes',
            'cuatro'    => '4 clientes',
            'ninguno'   => 'None',
            'sin adicional' => 'None',
            'none'      => 'None',
        ];

        $this->mealPlans = [
            'solo habitacion' => 'Room only',
            'room only'       => 'Room only',
            'desayuno'        => 'Breakfast',
            'half board'      => 'Half Board',
            'desayuno y cena' => 'Half Board',
            'media pension'   => 'Half Board',
            'full board'      => 'Full Board',
            'comidas completas'=> 'Full Board',
            'pension completa'=> 'Full Board',
        ];

        $this->knowledge = [
            'descripcion' => 'El Hotel Andino es un hotel de estilo contemporáneo ubicado en Puerto Boyacá, Boyacá. Combinamos un ' .
                'ambiente acogedor con amenidades modernas para viajeros de negocio y turismo.',
            'ubicacion' => 'Estamos en Puerto Boyacá, en el departamento de Boyacá, Colombia. Desde el centro de la ciudad puedes ' .
                'tomar la vía principal hacia el malecón y encontrarás el hotel a pocas cuadras, cerca del río Magdalena.',
            'servicios' => [
                'Piscina con zona de solárium',
                'Spa con cabinas de masajes y sauna',
                'Restaurante y room service disponibles 24/7',
                'Gimnasio equipado disponible 24/7',
                'Servicio de transporte en helicóptero bajo reserva previa',
                'Conectividad Wi-Fi de alta velocidad en todas las áreas',
            ],
            'habitaciones' => [
                'Habitación Doble: ideal para dos huéspedes, con cama queen, escritorio y vista urbana.',
                'Habitación Suite: espacio amplio con sala independiente, cama king y amenidades premium.',
                'Habitación Múltiple: perfecta para familias o grupos, configuraciones flexibles hasta cuatro personas.',
                'Habitación Sencilla: opción práctica para viajeros individuales con todas las comodidades esenciales.',
            ],
            'check' => 'El check-in se realiza desde las 15:00 y el check-out hasta las 12:00. Si necesitas horarios distintos, ' .
                'cuéntanos y coordinamos con recepción.',
            'contacto' => 'Puedes escribirnos a reservas@hotelandino.com o llamarnos al (+57) 320 555 0198. Nuestro equipo de recepción ' .
                'atiende 24/7.',
            'indicaciones' => 'Para llegar desde el centro de Puerto Boyacá dirígete hacia el malecón por la Carrera 8. Al pasar la plaza ' .
                'principal verás señalización del Hotel Andino a tu derecha; contamos con estacionamiento vigilado.',
            'turismo' => 'En la sección de experiencias (turismo.php) encontrarás atractivos cercanos, recorridos por el río Magdalena y ' .
                'recomendaciones personalizadas según tus intereses.',
        ];

        $this->userId = $this->resolveUserId();
        $this->activeReservation = $this->userId ? guest_portal_active_reservation($conn, $this->userId) : null;
    }

    public function handle(string $message): string
    {
        $message = trim($message);
        if ($message === '') {
            return 'Cuéntame en qué puedo ayudarte: información del hotel, servicios o si deseas reservar una habitación.';
        }

        $normalized = mb_strtolower($message, 'UTF-8');

        if ($this->isResetCommand($normalized)) {
            $this->resetBooking();
            return 'He reiniciado la conversación. ¿Necesitas información del hotel o deseas iniciar una reserva?';
        }

        if ($this->isCancelling($normalized)) {
            if ($this->isBookingActive()) {
                $this->resetBooking();
                return 'No hay problema, cancelé la reserva en curso. Si necesitas retomarla solo dime “reservar habitación”.';
            }
        }

        if ($this->isBookingActive()) {
            return $this->continueBooking($message);
        }

        if ($this->shouldStartBooking($normalized)) {
            return $this->startBooking();
        }

        if ($this->isReservationStatusQuestion($normalized)) {
            return $this->reservationStatusMessage();
        }

        if ($this->contains($normalized, ['hola', 'buenos días', 'buenas tardes', 'buenas noches', 'hey', 'saludos'])) {
            return '¡Hola! Soy el asistente virtual del Hotel Andino. Puedo darte detalles del hotel, servicios o ayudarte a reservar.';
        }

        if ($this->contains($normalized, ['dónde están', 'donde estan', 'ubicación', 'ubicacion', 'ciudad', 'puerto boyacá', 'puerto boyaca'])) {
            return $this->knowledge['ubicacion'];
        }

        if ($this->contains($normalized, ['qué es el hotel', 'que es el hotel', 'sobre el hotel', 'quienes son', 'información del hotel', 'informacion del hotel'])) {
            return $this->knowledge['descripcion'];
        }

        if ($this->contains($normalized, ['habitaciones', 'tipos de habitación', 'tipo de habitación', 'cuartos disponibles', 'habitacion', 'habitaciones disponibles'])) {
            return 'Contamos con estas opciones de habitación: ' . $this->formatList($this->knowledge['habitaciones']);
        }

        if ($this->contains($normalized, ['servicios', 'amenidades', 'instalaciones', 'spa', 'piscina', 'gimnasio', 'restaurante'])) {
            return 'Nuestros servicios destacados incluyen: ' . $this->formatList($this->knowledge['servicios']);
        }

        if ($this->contains($normalized, ['check in', 'check-in', 'check out', 'check-out', 'horario de llegada', 'horario de salida'])) {
            return $this->knowledge['check'];
        }

        if ($this->contains($normalized, ['contacto', 'teléfono', 'telefono', 'correo', 'email', 'whatsapp'])) {
            return $this->knowledge['contacto'];
        }

        if ($this->contains($normalized, ['cómo llegar', 'como llegar', 'indicaciones', 'llegar al hotel', 'direccion'])) {
            return $this->knowledge['indicaciones'];
        }

        if ($this->contains($normalized, ['actividades', 'planes', 'turismo', 'qué hacer', 'que hacer', 'lugares cercanos'])) {
            return $this->knowledge['turismo'];
        }

        if ($this->contains($normalized, ['precio', 'tarifa', 'costo', 'cuánto vale', 'cuanto vale', 'valores'])) {
            return 'Las tarifas cambian según temporada y disponibilidad. Dime qué fechas te interesan o inicia una reserva y te ' .
                'contactaremos con la cotización exacta.';
        }

        if ($this->contains($normalized, ['ayuda', 'qué puedes hacer', 'que puedes hacer', 'opciones'])) {
            return 'Puedo compartir información del Hotel Andino, detallar servicios, contarte sobre experiencias turísticas y gestionar ' .
                'una solicitud de reserva paso a paso.';
        }

        return 'Puedo ayudarte con información del hotel, servicios disponibles y reservas. Si quieres reservar dime “reservar habitación”.';
    }

    private function startBooking(): string
    {
        $_SESSION[self::SESSION_KEY] = [
            'step' => 'name',
            'data' => [],
        ];

        return 'Perfecto, iniciemos tu reserva. ¿Cuál es tu nombre completo? (Escribe “cancelar” para detener el proceso.)';
    }

    private function continueBooking(string $message): string
    {
        $state = $_SESSION[self::SESSION_KEY] ?? ['step' => 'name', 'data' => []];
        $step = $state['step'] ?? 'name';
        $data = $state['data'] ?? [];

        switch ($step) {
            case 'name':
                $value = trim($message);
                if (mb_strlen($value, 'UTF-8') < 3) {
                    return 'Necesito un nombre y apellido para la reserva. Por favor escríbelo completo.';
                }
                $data['Name'] = $value;
                $nextPrompt = 'Gracias, ' . $value . '. ¿Cuál es tu correo electrónico de contacto?';
                $nextStep = 'email';
                break;

            case 'email':
                $email = filter_var(trim($message), FILTER_VALIDATE_EMAIL);
                if (!$email) {
                    return 'El correo no parece válido. Escríbelo con formato nombre@dominio.com.';
                }
                $data['Email'] = $email;
                $nextPrompt = '¿Desde qué país nos visitas?';
                $nextStep = 'country';
                break;

            case 'country':
                $country = trim($message);
                if ($country === '') {
                    return 'Indica el país de procedencia para registrar la reserva.';
                }
                $data['Country'] = $country;
                $nextPrompt = '¿Deseas registrar un número de teléfono? (puedes escribir “omitir”).';
                $nextStep = 'phone';
                break;

            case 'phone':
                $phone = trim($message);
                if ($phone === '' || $this->isSkipWord($phone)) {
                    $data['Phone'] = '';
                } else {
                    $sanitized = preg_replace('/[^0-9+\s()-]/', '', $phone);
                    if ($sanitized === '') {
                        return 'Si deseas compartirlo, escribe el número incluyendo indicativo. También puedes responder “omitir”.';
                    }
                    $data['Phone'] = $sanitized;
                }
                $options = implode(', ', array_unique(array_values($this->roomTypes)));
                $nextPrompt = '¿Qué tipo de habitación prefieres? Opciones: ' . $options . '.';
                $nextStep = 'room_type';
                break;

            case 'room_type':
                $room = $this->matchOption($message, $this->roomTypes);
                if ($room === null) {
                    return 'Indica uno de los tipos disponibles (Doble, Suite, Múltiple o Sencilla).';
                }
                $data['RoomType'] = $room;
                $nextPrompt = '¿Para cuántas personas necesitas la habitación? (1, 2, 3, 4 clientes o “sin adicional”).';
                $nextStep = 'bed';
                break;

            case 'bed':
                $bed = $this->matchOption($message, $this->bedOptions);
                if ($bed === null) {
                    return 'Responde con 1, 2, 3, 4 clientes o indica “sin adicional”.';
                }
                $data['Bed'] = $bed;
                $nextPrompt = '¿Cuántas habitaciones del mismo tipo necesitas? (normalmente 1).';
                $nextStep = 'rooms';
                break;

            case 'rooms':
                $count = (int) filter_var($message, FILTER_SANITIZE_NUMBER_INT);
                if ($count < 1) {
                    return 'Indica un número válido de habitaciones (por ejemplo 1).';
                }
                $data['NoofRoom'] = $count;
                $options = implode(', ', array_unique(array_values($this->mealPlans)));
                $nextPrompt = '¿Qué plan de alimentación prefieres? Opciones: ' . $options . '.';
                $nextStep = 'meal';
                break;

            case 'meal':
                $meal = $this->matchOption($message, $this->mealPlans);
                if ($meal === null) {
                    return 'Elige entre Solo habitación, Desayuno, Desayuno y Cena o Comidas completas.';
                }
                $data['Meal'] = $meal;
                $nextPrompt = '¿Cuál es la fecha de llegada? (formato AAAA-MM-DD o DD/MM/AAAA).';
                $nextStep = 'check_in';
                break;

            case 'check_in':
                $cin = $this->parseDate($message);
                if ($cin === null) {
                    return 'No pude reconocer la fecha de llegada. Usa el formato AAAA-MM-DD o DD/MM/AAAA.';
                }
                $data['cin'] = $cin;
                $nextPrompt = '¿Cuál es la fecha de salida? (formato AAAA-MM-DD o DD/MM/AAAA).';
                $nextStep = 'check_out';
                break;

            case 'check_out':
                $cout = $this->parseDate($message);
                if ($cout === null) {
                    return 'No pude reconocer la fecha de salida. Usa el formato AAAA-MM-DD o DD/MM/AAAA.';
                }
                $data['cout'] = $cout;
                $this->resetBooking();
                return $this->finalizeBooking($data);

            default:
                $this->resetBooking();
                return 'Ocurrió algo inesperado, reiniciemos la reserva. ¿Deseas intentarlo de nuevo?';
        }

        $_SESSION[self::SESSION_KEY] = [
            'step' => $nextStep,
            'data' => $data,
        ];

        return $nextPrompt;
    }

    private function finalizeBooking(array $data): string
    {
        $cin = strtotime($data['cin']);
        $cout = strtotime($data['cout']);
        if ($cin === false || $cout === false || $cout <= $cin) {
            return 'La fecha de salida debe ser posterior a la llegada. Vuelve a iniciar la reserva para corregir las fechas.';
        }
        $nodays = (int) round(($cout - $cin) / 86400);

        $userId = $this->userId;
        $sta = 'NotConfirm';

        $sql = 'INSERT INTO roombook (user_id, Name, Email, Country, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, stat, nodays) '
            . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return 'No pude registrar la reserva en este momento. Intenta nuevamente en unos minutos.';
        }

        $phone = $data['Phone'] ?? '';
        if (!is_string($phone)) {
            $phone = '';
        }

        $stmt->bind_param(
            'issssssissssi',
            $userId,
            $data['Name'],
            $data['Email'],
            $data['Country'],
            $phone,
            $data['RoomType'],
            $data['Bed'],
            $data['NoofRoom'],
            $data['Meal'],
            $data['cin'],
            $data['cout'],
            $sta,
            $nodays
        );

        $ok = $stmt->execute();
        $reservationId = $ok ? (int) $stmt->insert_id : 0;
        $stmt->close();

        if (!$ok || $reservationId <= 0) {
            return 'No pude registrar la reserva en la base de datos. Por favor intenta nuevamente más tarde.';
        }

        $notifMessage = sprintf('Nueva reserva pendiente de %s (%s)', $data['Name'], $data['Email']);
        guest_portal_record_notification($this->conn, 'admin', 'Reserva solicitada', $notifMessage, 'admin/roombook.php', $reservationId, null);
        guest_portal_record_notification($this->conn, 'recepcion', 'Reserva solicitada', $notifMessage, 'admin/roombook.php', $reservationId, null);

        $summary = sprintf(
            'Reserva registrada para %s (%s). Tipo: %s · Plan: %s · Llegada %s · Salida %s. Nuestro equipo confirmará la disponibilidad y tarifas en breve.',
            $data['Name'],
            $data['Email'],
            $data['RoomType'],
            $data['Meal'],
            $this->formatDateForUser($data['cin']),
            $this->formatDateForUser($data['cout'])
        );

        return $summary;
    }

    private function reservationStatusMessage(): string
    {
        if (!$this->userId) {
            return 'Para consultar el estado de tus reservas inicia sesión con tu cuenta y dirígete a la sección “Mis reservas” en el portal de huéspedes.';
        }

        if (!$this->activeReservation) {
            return 'No encuentro reservas activas asociadas a tu cuenta en este momento. Puedes iniciar una nueva reserva cuando lo desees.';
        }

        $status = $this->activeReservation['stat'] ?? 'NotConfirm';
        $label = match ($status) {
            'Confirm' => 'confirmada',
            'Ocupado', 'CheckIn' => 'en curso',
            'NotConfirm' => 'pendiente de confirmación',
            default => strtolower($status),
        };

        $room = $this->activeReservation['RoomType']
            ?? $this->activeReservation['room_type_name']
            ?? 'habitación';
        $cin = $this->activeReservation['cin'] ?? '';
        $cout = $this->activeReservation['cout'] ?? '';

        return sprintf(
            'Tu reserva más reciente es %s para la %s. Check-in %s y check-out %s. Si necesitas ajustes avísanos por este medio.',
            $label,
            $room,
            $cin ? $this->formatDateForUser($cin) : 'sin fecha registrada',
            $cout ? $this->formatDateForUser($cout) : 'sin fecha registrada'
        );
    }

    private function isBookingActive(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) && is_array($_SESSION[self::SESSION_KEY]);
    }

    private function resetBooking(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    private function isResetCommand(string $normalized): bool
    {
        return $this->contains($normalized, ['reiniciar chat', 'reinicia', 'reset', 'comenzar de nuevo']);
    }

    private function isCancelling(string $normalized): bool
    {
        return $this->contains($normalized, ['cancelar', 'detener', 'no continuar', 'parar reserva']);
    }

    private function shouldStartBooking(string $normalized): bool
    {
        if ($this->contains($normalized, ['mi reserva', 'estado de mi reserva', 'tengo una reserva'])) {
            return false;
        }
        return $this->contains($normalized, ['reservar', 'hacer una reserva', 'quiero reservar', 'book', 'necesito una habitación', 'necesito habitacion']);
    }

    private function isReservationStatusQuestion(string $normalized): bool
    {
        return $this->contains($normalized, ['mi reserva', 'estado de mi reserva', 'reserva actual', 'tengo una reserva', 'ver mi reserva']);
    }

    private function matchOption(string $input, array $options): ?string
    {
        $normalized = mb_strtolower(trim($input), 'UTF-8');
        foreach ($options as $key => $value) {
            if ($normalized === $key) {
                return $value;
            }
        }
        foreach ($options as $key => $value) {
            if ($key !== '' && mb_strpos($normalized, $key) !== false) {
                return $value;
            }
        }
        return null;
    }

    private function parseDate(string $input): ?string
    {
        $value = trim($input);
        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y'];
        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $value);
            if ($dt instanceof DateTime) {
                $errors = DateTime::getLastErrors();
                if ($errors['warning_count'] === 0 && $errors['error_count'] === 0) {
                    return $dt->format('Y-m-d');
                }
            }
        }

        if (preg_match('/^\d{4}\d{2}\d{2}$/', $value)) {
            $dt = DateTime::createFromFormat('Ymd', $value);
            if ($dt instanceof DateTime) {
                return $dt->format('Y-m-d');
            }
        }

        return null;
    }

    private function formatDateForUser(string $date): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date) ?: DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if ($dt instanceof DateTime) {
            return $dt->format('d/m/Y');
        }
        return $date;
    }

    private function formatList(array $items): string
    {
        return implode('; ', $items);
    }

    private function isSkipWord(string $value): bool
    {
        $normalized = mb_strtolower(trim($value), 'UTF-8');
        return in_array($normalized, ['omitir', 'skip', 'ninguno', 'no', 'prefiero no'], true);
    }

    private function contains(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    private function resolveUserId(): ?int
    {
        $email = $_SESSION['usermail'] ?? null;
        if (!is_string($email) || $email === '') {
            return null;
        }

        $user = guest_portal_fetch_user($this->conn, $email);
        if (!$user) {
            return null;
        }

        $id = $user['UserID'] ?? null;
        return is_numeric($id) ? (int) $id : null;
    }
}
