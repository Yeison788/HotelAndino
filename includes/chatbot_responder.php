<?php
declare(strict_types=1);

/**
 * Chatbot especializado del Hotel Andino.
<<<<<<< ours
 * Gestiona respuestas frecuentes y reserva de habitaciones sin depender de APIs externas.
 */
class HotelChatbotResponder
{
    private const SESSION_KEY = 'chatbot_booking';
=======
 * Gestiona respuestas frecuentes, reserva de habitaciones y solicitudes a la habitación sin depender de APIs externas.
 */
class HotelChatbotResponder
{
    private const BOOKING_SESSION_KEY = 'chatbot_booking';
    private const SERVICE_SESSION_KEY = 'chatbot_service';
>>>>>>> theirs

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

<<<<<<< ours
=======
    /** @var array<string, string> */
    private array $serviceTypeLabels;

    /** @var array<string, string> */
    private array $serviceTypeKeywords;

    /** @var array<int, array<string, string>> */
    private array $defaultSuggestions;

>>>>>>> theirs
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
        guest_portal_ensure_schema($conn);

        $this->roomTypes = [
<<<<<<< ours
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
=======
            'habitacion doble'     => 'Habitación Doble',
            'habitación doble'     => 'Habitación Doble',
            'doble'                => 'Habitación Doble',
            'habitacion suite'     => 'Habitación Suite',
            'habitación suite'     => 'Habitación Suite',
            'suite'                => 'Habitación Suite',
            'habitacion multiple'  => 'Habitación Múltiple',
            'habitación múltiple'  => 'Habitación Múltiple',
            'multiple'             => 'Habitación Múltiple',
            'habitacion sencilla'  => 'Habitación Sencilla',
            'habitación sencilla'  => 'Habitación Sencilla',
            'sencilla'             => 'Habitación Sencilla',
        ];

        $this->bedOptions = [
            '1 cliente'    => '1 cliente',
            'uno'          => '1 cliente',
            '1'            => '1 cliente',
            '2 clientes'   => '2 clientes',
            'dos'          => '2 clientes',
            '2'            => '2 clientes',
            '3 clientes'   => '3 clientes',
            'tres'         => '3 clientes',
            '3'            => '3 clientes',
            '4 clientes'   => '4 clientes',
            'cuatro'       => '4 clientes',
            '4'            => '4 clientes',
            'sin adicional'=> 'None',
            'sin adicional extra' => 'None',
            'ninguno'      => 'None',
            'none'         => 'None',
        ];

        $this->mealPlans = [
            'solo habitacion'     => 'Room only',
            'solo habitación'     => 'Room only',
            'room only'           => 'Room only',
            'sin comidas'         => 'Room only',
            'desayuno'            => 'Breakfast',
            'desayuno incluido'   => 'Breakfast',
            'breakfast'           => 'Breakfast',
            'desayuno y cena'     => 'Half Board',
            'media pension'       => 'Half Board',
            'media pensión'       => 'Half Board',
            'half board'          => 'Half Board',
            'pension completa'    => 'Full Board',
            'pensión completa'    => 'Full Board',
            'comidas completas'   => 'Full Board',
            'full board'          => 'Full Board',
        ];

        $this->serviceTypeLabels = [
            'room_service' => 'Servicio a la habitación',
            'toalla'       => 'Toallas adicionales',
            'jabon'        => 'Artículos de aseo',
            'asistencia'   => 'Asistencia de recepción',
            'minibar'      => 'Atención al minibar',
            'otro'         => 'Otro servicio',
        ];

        $this->serviceTypeKeywords = [
            'servicio a la habitación' => 'room_service',
            'servicio a la habitacion' => 'room_service',
            'room service'             => 'room_service',
            'desayuno a la habitación' => 'room_service',
            'desayuno a la habitacion' => 'room_service',
            'enviar comida'            => 'room_service',
            'pedir comida'             => 'room_service',
            'pedido a la habitación'   => 'room_service',
            'pedido a la habitacion'   => 'room_service',
            'toalla adicional'         => 'toalla',
            'toallas adicionales'      => 'toalla',
            'toalla'                   => 'toalla',
            'jabón'                    => 'jabon',
            'jabon'                    => 'jabon',
            'amenities'                => 'jabon',
            'artículos de aseo'        => 'jabon',
            'articulos de aseo'        => 'jabon',
            'recepción'                => 'asistencia',
            'recepcion'                => 'asistencia',
            'ayuda de recepción'       => 'asistencia',
            'ayuda de recepcion'       => 'asistencia',
            'minibar'                  => 'minibar',
            'otro servicio'            => 'otro',
            'otro'                     => 'otro',
        ];

        $this->knowledge = [
            'descripcion' => 'El Hotel Andino es un hotel de estilo contemporáneo ubicado en Puerto Boyacá, Boyacá. Combinamos un ambiente acogedor con amenidades modernas para viajeros de negocio y turismo.',
            'ubicacion' => 'Estamos en Puerto Boyacá, en el departamento de Boyacá, Colombia. Desde el centro de la ciudad puedes tomar la vía principal hacia el malecón y encontrarás el hotel a pocas cuadras, cerca del río Magdalena.',
            'servicios' => [
                'Piscina con zona de solárium',
                'Spa con cabinas de masajes y sauna',
                'Restaurante con room service 24/7',
>>>>>>> theirs
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
<<<<<<< ours
            'check' => 'El check-in se realiza desde las 15:00 y el check-out hasta las 12:00. Si necesitas horarios distintos, ' .
                'cuéntanos y coordinamos con recepción.',
            'contacto' => 'Puedes escribirnos a reservas@hotelandino.com o llamarnos al (+57) 320 555 0198. Nuestro equipo de recepción ' .
                'atiende 24/7.',
            'indicaciones' => 'Para llegar desde el centro de Puerto Boyacá dirígete hacia el malecón por la Carrera 8. Al pasar la plaza ' .
                'principal verás señalización del Hotel Andino a tu derecha; contamos con estacionamiento vigilado.',
            'turismo' => 'En la sección de experiencias (turismo.php) encontrarás atractivos cercanos, recorridos por el río Magdalena y ' .
                'recomendaciones personalizadas según tus intereses.',
=======
            'check' => 'El check-in se realiza desde las 15:00 y el check-out hasta las 12:00. Si necesitas horarios distintos, cuéntanos y coordinamos con recepción.',
            'contacto' => 'Puedes escribirnos a reservas@hotelandino.com o llamarnos al (+57) 320 555 0198. Nuestro equipo de recepción atiende 24/7.',
            'indicaciones' => 'Para llegar desde el centro de Puerto Boyacá dirígete hacia el malecón por la Carrera 8. Al pasar la plaza principal verás señalización del Hotel Andino a tu derecha; contamos con estacionamiento vigilado.',
            'turismo' => 'En la sección de experiencias (turismo.php) encontrarás atractivos cercanos, recorridos por el río Magdalena y recomendaciones personalizadas según tus intereses.',
            'gastronomia' => 'Nuestro restaurante ofrece desayuno típico colombiano, opciones saludables, almuerzos ejecutivos y cenas gourmet. También podemos llevar platos y bebidas a tu habitación las 24 horas.',
>>>>>>> theirs
        ];

        $this->userId = $this->resolveUserId();
        $this->activeReservation = $this->userId ? guest_portal_active_reservation($conn, $this->userId) : null;
<<<<<<< ours
    }

    public function handle(string $message): string
    {
        $message = trim($message);
        if ($message === '') {
            return 'Cuéntame en qué puedo ayudarte: información del hotel, servicios o si deseas reservar una habitación.';
=======
        $this->defaultSuggestions = $this->buildDefaultSuggestions();
    }

    /**
     * @return array{message:string,suggestions:array<int,array{label:string,value:string}>,meta:array<string,string>}
     */
    public function handle(string $message): array
    {
        $message = trim($message);
        if ($message === '') {
            return $this->respond(
                'Cuéntame en qué puedo ayudarte: información del hotel, servicios o si deseas reservar una habitación.',
                $this->defaultSuggestions,
            );
>>>>>>> theirs
        }

        $normalized = mb_strtolower($message, 'UTF-8');

        if ($this->isResetCommand($normalized)) {
            $this->resetBooking();
<<<<<<< ours
            return 'He reiniciado la conversación. ¿Necesitas información del hotel o deseas iniciar una reserva?';
=======
            $this->resetService();
            return $this->respond(
                'He reiniciado la conversación. ¿Necesitas información del hotel, deseas reservar o prefieres solicitar algo a tu habitación?',
                $this->defaultSuggestions,
            );
>>>>>>> theirs
        }

        if ($this->isCancelling($normalized)) {
            if ($this->isBookingActive()) {
                $this->resetBooking();
<<<<<<< ours
                return 'No hay problema, cancelé la reserva en curso. Si necesitas retomarla solo dime “reservar habitación”.';
=======
                return $this->respond(
                    'No hay problema, cancelé la reserva en curso. Si necesitas retomarla solo dime “reservar habitación”.',
                    $this->defaultSuggestions,
                );
            }
            if ($this->isServiceActive()) {
                $this->resetService();
                return $this->respond(
                    'Cancelé la solicitud en curso. Si necesitas pedir algo a la habitación vuelve a indicármelo.',
                    $this->defaultSuggestions,
                );
>>>>>>> theirs
            }
        }

        if ($this->isBookingActive()) {
            return $this->continueBooking($message);
        }

<<<<<<< ours
=======
        if ($this->isServiceActive()) {
            return $this->continueServiceRequest($message);
        }

>>>>>>> theirs
        if ($this->shouldStartBooking($normalized)) {
            return $this->startBooking();
        }

<<<<<<< ours
=======
        if ($this->shouldStartServiceRequest($normalized)) {
            return $this->startServiceRequest();
        }

>>>>>>> theirs
        if ($this->isReservationStatusQuestion($normalized)) {
            return $this->reservationStatusMessage();
        }

        if ($this->contains($normalized, ['hola', 'buenos días', 'buenas tardes', 'buenas noches', 'hey', 'saludos'])) {
<<<<<<< ours
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
=======
            return $this->respond(
                '¡Hola! Soy el asistente virtual del Hotel Andino. Puedo darte detalles del hotel, servicios, ayudarte a reservar o solicitar algo para tu habitación.',
                $this->defaultSuggestions,
            );
        }

        if ($this->contains($normalized, ['dónde están', 'donde estan', 'ubicación', 'ubicacion', 'ciudad', 'puerto boyacá', 'puerto boyaca'])) {
            return $this->respond($this->knowledge['ubicacion'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['qué es el hotel', 'que es el hotel', 'sobre el hotel', 'quienes son', 'información del hotel', 'informacion del hotel'])) {
            return $this->respond($this->knowledge['descripcion'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['habitaciones', 'tipos de habitación', 'tipo de habitación', 'cuartos disponibles', 'habitacion', 'habitaciones disponibles'])) {
            return $this->respond('Contamos con estas opciones de habitación: ' . $this->formatList($this->knowledge['habitaciones']), $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['servicios', 'amenidades', 'instalaciones', 'spa', 'piscina', 'gimnasio', 'restaurante'])) {
            return $this->respond('Nuestros servicios destacados incluyen: ' . $this->formatList($this->knowledge['servicios']), $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['desayuno', 'restaurante', 'comida', 'cena', 'almuerzo', 'gastronomía'])) {
            return $this->respond($this->knowledge['gastronomia'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['check in', 'check-in', 'check out', 'check-out', 'horario de llegada', 'horario de salida'])) {
            return $this->respond($this->knowledge['check'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['contacto', 'teléfono', 'telefono', 'correo', 'email', 'whatsapp'])) {
            return $this->respond($this->knowledge['contacto'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['cómo llegar', 'como llegar', 'indicaciones', 'llegar al hotel', 'direccion'])) {
            return $this->respond($this->knowledge['indicaciones'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['actividades', 'planes', 'turismo', 'qué hacer', 'que hacer', 'lugares cercanos'])) {
            return $this->respond($this->knowledge['turismo'], $this->defaultSuggestions);
        }

        if ($this->contains($normalized, ['precio', 'tarifa', 'costo', 'cuánto vale', 'cuanto vale', 'valores'])) {
            return $this->respond(
                'Las tarifas cambian según temporada y disponibilidad. Dime qué fechas te interesan o inicia una reserva y te contactaremos con la cotización exacta.',
                $this->defaultSuggestions,
            );
        }

        if ($this->contains($normalized, ['ayuda', 'qué puedes hacer', 'que puedes hacer', 'opciones'])) {
            return $this->respond(
                'Puedo compartir información del Hotel Andino, detallar servicios, contarte sobre experiencias turísticas, guiarte en una reserva o solicitar algo para tu habitación.',
                $this->defaultSuggestions,
            );
        }

        return $this->respond(
            'Puedo ayudarte con información del hotel, servicios disponibles, reservas o solicitudes a la habitación. Si quieres reservar, dime “reservar habitación”.',
            $this->defaultSuggestions,
        );
    }

    private function startBooking(): array
    {
        $_SESSION[self::BOOKING_SESSION_KEY] = [
>>>>>>> theirs
            'step' => 'name',
            'data' => [],
        ];

<<<<<<< ours
        return 'Perfecto, iniciemos tu reserva. ¿Cuál es tu nombre completo? (Escribe “cancelar” para detener el proceso.)';
    }

    private function continueBooking(string $message): string
    {
        $state = $_SESSION[self::SESSION_KEY] ?? ['step' => 'name', 'data' => []];
=======
        return $this->respond(
            'Perfecto, iniciemos tu reserva. ¿Cuál es tu nombre completo? (Escribe “cancelar” para detener el proceso.)',
            [],
            ['inputType' => 'text', 'placeholder' => 'Nombre y apellido']
        );
    }

    private function continueBooking(string $message): array
    {
        $state = $_SESSION[self::BOOKING_SESSION_KEY] ?? ['step' => 'name', 'data' => []];
>>>>>>> theirs
        $step = $state['step'] ?? 'name';
        $data = $state['data'] ?? [];

        switch ($step) {
            case 'name':
                $value = trim($message);
                if (mb_strlen($value, 'UTF-8') < 3) {
<<<<<<< ours
                    return 'Necesito un nombre y apellido para la reserva. Por favor escríbelo completo.';
=======
                    return $this->respond(
                        'Necesito un nombre y apellido para la reserva. Por favor escríbelo completo.',
                        [],
                        ['inputType' => 'text', 'placeholder' => 'Nombre y apellido']
                    );
>>>>>>> theirs
                }
                $data['Name'] = $value;
                $nextPrompt = 'Gracias, ' . $value . '. ¿Cuál es tu correo electrónico de contacto?';
                $nextStep = 'email';
<<<<<<< ours
=======
                $suggestions = [];
                $meta = ['inputType' => 'email', 'placeholder' => 'correo@ejemplo.com'];
>>>>>>> theirs
                break;

            case 'email':
                $email = filter_var(trim($message), FILTER_VALIDATE_EMAIL);
                if (!$email) {
<<<<<<< ours
                    return 'El correo no parece válido. Escríbelo con formato nombre@dominio.com.';
=======
                    return $this->respond(
                        'El correo no parece válido. Escríbelo con formato nombre@dominio.com.',
                        [],
                        ['inputType' => 'email', 'placeholder' => 'correo@ejemplo.com']
                    );
>>>>>>> theirs
                }
                $data['Email'] = $email;
                $nextPrompt = '¿Desde qué país nos visitas?';
                $nextStep = 'country';
<<<<<<< ours
=======
                $suggestions = [];
                $meta = ['inputType' => 'text', 'placeholder' => 'Escribe tu país'];
>>>>>>> theirs
                break;

            case 'country':
                $country = trim($message);
                if ($country === '') {
<<<<<<< ours
                    return 'Indica el país de procedencia para registrar la reserva.';
=======
                    return $this->respond(
                        'Indica el país de procedencia para registrar la reserva.',
                        [],
                        ['inputType' => 'text', 'placeholder' => 'Escribe tu país']
                    );
>>>>>>> theirs
                }
                $data['Country'] = $country;
                $nextPrompt = '¿Deseas registrar un número de teléfono? (puedes escribir “omitir”).';
                $nextStep = 'phone';
<<<<<<< ours
=======
                $suggestions = [
                    $this->suggestion('Omitir', 'Omitir'),
                ];
                $meta = ['inputType' => 'tel', 'placeholder' => 'Número con indicativo u “omitir”'];
>>>>>>> theirs
                break;

            case 'phone':
                $phone = trim($message);
                if ($phone === '' || $this->isSkipWord($phone)) {
                    $data['Phone'] = '';
                } else {
                    $sanitized = preg_replace('/[^0-9+\s()-]/', '', $phone);
                    if ($sanitized === '') {
<<<<<<< ours
                        return 'Si deseas compartirlo, escribe el número incluyendo indicativo. También puedes responder “omitir”.';
                    }
                    $data['Phone'] = $sanitized;
                }
                $options = implode(', ', array_unique(array_values($this->roomTypes)));
                $nextPrompt = '¿Qué tipo de habitación prefieres? Opciones: ' . $options . '.';
                $nextStep = 'room_type';
=======
                        return $this->respond(
                            'Si deseas compartirlo, escribe el número incluyendo indicativo. También puedes responder “omitir”.',
                            [$this->suggestion('Omitir', 'Omitir')],
                            ['inputType' => 'tel', 'placeholder' => 'Número con indicativo u “omitir”']
                        );
                    }
                    $data['Phone'] = $sanitized;
                }
                $options = $this->suggestionListFromValues(array_values($this->roomTypes));
                $nextPrompt = '¿Qué tipo de habitación prefieres? Elige una opción:';
                $nextStep = 'room_type';
                $suggestions = $options;
                $meta = ['inputType' => 'text', 'placeholder' => 'Selecciona un tipo de habitación'];
>>>>>>> theirs
                break;

            case 'room_type':
                $room = $this->matchOption($message, $this->roomTypes);
                if ($room === null) {
<<<<<<< ours
                    return 'Indica uno de los tipos disponibles (Doble, Suite, Múltiple o Sencilla).';
                }
                $data['RoomType'] = $room;
                $nextPrompt = '¿Para cuántas personas necesitas la habitación? (1, 2, 3, 4 clientes o “sin adicional”).';
                $nextStep = 'bed';
=======
                    return $this->respond(
                        'Indica uno de los tipos disponibles (Doble, Suite, Múltiple o Sencilla).',
                        $this->suggestionListFromValues(array_values($this->roomTypes)),
                        ['inputType' => 'text', 'placeholder' => 'Selecciona un tipo de habitación']
                    );
                }
                $data['RoomType'] = $room;
                $suggestions = [
                    $this->suggestion('1 cliente', '1 cliente'),
                    $this->suggestion('2 clientes', '2 clientes'),
                    $this->suggestion('3 clientes', '3 clientes'),
                    $this->suggestion('4 clientes', '4 clientes'),
                    $this->suggestion('Sin adicional', 'Sin adicional'),
                ];
                $nextPrompt = '¿Para cuántas personas necesitas la habitación? (1, 2, 3, 4 clientes o “sin adicional”).';
                $nextStep = 'bed';
                $meta = ['inputType' => 'text', 'placeholder' => 'Selecciona la capacidad'];
>>>>>>> theirs
                break;

            case 'bed':
                $bed = $this->matchOption($message, $this->bedOptions);
                if ($bed === null) {
<<<<<<< ours
                    return 'Responde con 1, 2, 3, 4 clientes o indica “sin adicional”.';
                }
                $data['Bed'] = $bed;
                $nextPrompt = '¿Cuántas habitaciones del mismo tipo necesitas? (normalmente 1).';
                $nextStep = 'rooms';
=======
                    return $this->respond(
                        'Responde con 1, 2, 3, 4 clientes o indica “sin adicional”.',
                        [
                            $this->suggestion('1 cliente', '1'),
                            $this->suggestion('2 clientes', '2'),
                            $this->suggestion('3 clientes', '3'),
                            $this->suggestion('4 clientes', '4'),
                            $this->suggestion('Sin adicional', 'Sin adicional'),
                        ],
                        ['inputType' => 'text', 'placeholder' => 'Selecciona la capacidad']
                    );
                }
                $data['Bed'] = $bed;
                $suggestions = [
                    $this->suggestion('1', '1'),
                    $this->suggestion('2', '2'),
                    $this->suggestion('3', '3'),
                ];
                $nextPrompt = '¿Cuántas habitaciones del mismo tipo necesitas? (normalmente 1).';
                $nextStep = 'rooms';
                $meta = ['inputType' => 'number', 'placeholder' => 'Indica un número'];
>>>>>>> theirs
                break;

            case 'rooms':
                $count = (int) filter_var($message, FILTER_SANITIZE_NUMBER_INT);
                if ($count < 1) {
<<<<<<< ours
                    return 'Indica un número válido de habitaciones (por ejemplo 1).';
                }
                $data['NoofRoom'] = $count;
                $options = implode(', ', array_unique(array_values($this->mealPlans)));
                $nextPrompt = '¿Qué plan de alimentación prefieres? Opciones: ' . $options . '.';
                $nextStep = 'meal';
=======
                    return $this->respond(
                        'Indica un número válido de habitaciones (por ejemplo 1).',
                        [
                            $this->suggestion('1', '1'),
                            $this->suggestion('2', '2'),
                            $this->suggestion('3', '3'),
                        ],
                        ['inputType' => 'number', 'placeholder' => 'Indica un número entero']
                    );
                }
                $data['NoofRoom'] = $count;
                $suggestions = [
                    $this->suggestion('Solo habitación', 'Solo habitación'),
                    $this->suggestion('Desayuno incluido', 'Desayuno incluido'),
                    $this->suggestion('Desayuno y cena', 'Desayuno y cena'),
                    $this->suggestion('Pensión completa', 'Pensión completa'),
                ];
                $nextPrompt = '¿Qué plan de alimentación prefieres?';
                $nextStep = 'meal';
                $meta = ['inputType' => 'text', 'placeholder' => 'Selecciona un plan de alimentación'];
>>>>>>> theirs
                break;

            case 'meal':
                $meal = $this->matchOption($message, $this->mealPlans);
                if ($meal === null) {
<<<<<<< ours
                    return 'Elige entre Solo habitación, Desayuno, Desayuno y Cena o Comidas completas.';
                }
                $data['Meal'] = $meal;
                $nextPrompt = '¿Cuál es la fecha de llegada? (formato AAAA-MM-DD o DD/MM/AAAA).';
                $nextStep = 'check_in';
=======
                    return $this->respond(
                        'Elige entre Solo habitación, Desayuno incluido, Desayuno y cena o Pensión completa.',
                        [
                            $this->suggestion('Solo habitación', 'Solo habitación'),
                            $this->suggestion('Desayuno incluido', 'Desayuno incluido'),
                            $this->suggestion('Desayuno y cena', 'Desayuno y cena'),
                            $this->suggestion('Pensión completa', 'Pensión completa'),
                        ],
                        ['inputType' => 'text', 'placeholder' => 'Selecciona un plan de alimentación']
                    );
                }
                $data['Meal'] = $meal;
                $nextPrompt = '¿Cuál es la fecha de llegada? (usa el selector o formato AAAA-MM-DD).';
                $nextStep = 'check_in';
                $suggestions = [];
                $meta = ['inputType' => 'date', 'placeholder' => 'Selecciona la fecha de llegada'];
>>>>>>> theirs
                break;

            case 'check_in':
                $cin = $this->parseDate($message);
                if ($cin === null) {
<<<<<<< ours
                    return 'No pude reconocer la fecha de llegada. Usa el formato AAAA-MM-DD o DD/MM/AAAA.';
                }
                $data['cin'] = $cin;
                $nextPrompt = '¿Cuál es la fecha de salida? (formato AAAA-MM-DD o DD/MM/AAAA).';
                $nextStep = 'check_out';
=======
                    return $this->respond(
                        'No pude reconocer la fecha de llegada. Usa el selector o el formato AAAA-MM-DD.',
                        [],
                        ['inputType' => 'date', 'placeholder' => 'Selecciona la fecha de llegada']
                    );
                }
                $data['cin'] = $cin;
                $nextPrompt = '¿Cuál es la fecha de salida? (usa el selector o formato AAAA-MM-DD).';
                $nextStep = 'check_out';
                $suggestions = [];
                $meta = ['inputType' => 'date', 'placeholder' => 'Selecciona la fecha de salida'];
>>>>>>> theirs
                break;

            case 'check_out':
                $cout = $this->parseDate($message);
                if ($cout === null) {
<<<<<<< ours
                    return 'No pude reconocer la fecha de salida. Usa el formato AAAA-MM-DD o DD/MM/AAAA.';
=======
                    return $this->respond(
                        'No pude reconocer la fecha de salida. Usa el selector o el formato AAAA-MM-DD.',
                        [],
                        ['inputType' => 'date', 'placeholder' => 'Selecciona la fecha de salida']
                    );
>>>>>>> theirs
                }
                $data['cout'] = $cout;
                $this->resetBooking();
                return $this->finalizeBooking($data);

            default:
                $this->resetBooking();
<<<<<<< ours
                return 'Ocurrió algo inesperado, reiniciemos la reserva. ¿Deseas intentarlo de nuevo?';
        }

        $_SESSION[self::SESSION_KEY] = [
=======
                return $this->respond(
                    'Ocurrió algo inesperado, reiniciemos la reserva. ¿Deseas intentarlo de nuevo?',
                    $this->defaultSuggestions
                );
        }

        $_SESSION[self::BOOKING_SESSION_KEY] = [
>>>>>>> theirs
            'step' => $nextStep,
            'data' => $data,
        ];

<<<<<<< ours
        return $nextPrompt;
    }

    private function finalizeBooking(array $data): string
=======
        return $this->respond($nextPrompt, $suggestions ?? [], $meta ?? []);
    }

    private function finalizeBooking(array $data): array
>>>>>>> theirs
    {
        $cin = strtotime($data['cin']);
        $cout = strtotime($data['cout']);
        if ($cin === false || $cout === false || $cout <= $cin) {
<<<<<<< ours
            return 'La fecha de salida debe ser posterior a la llegada. Vuelve a iniciar la reserva para corregir las fechas.';
=======
            return $this->respond(
                'La fecha de salida debe ser posterior a la llegada. Vuelve a iniciar la reserva para corregir las fechas.',
                $this->defaultSuggestions,
                ['inputType' => 'text']
            );
>>>>>>> theirs
        }
        $nodays = (int) round(($cout - $cin) / 86400);

        $userId = $this->userId;
        $sta = 'NotConfirm';

        $sql = 'INSERT INTO roombook (user_id, Name, Email, Country, Phone, RoomType, Bed, NoofRoom, Meal, cin, cout, stat, nodays) '
            . 'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
<<<<<<< ours
            return 'No pude registrar la reserva en este momento. Intenta nuevamente en unos minutos.';
=======
            return $this->respond(
                'No pude registrar la reserva en este momento. Intenta nuevamente en unos minutos.',
                $this->defaultSuggestions
            );
>>>>>>> theirs
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
<<<<<<< ours
            return 'No pude registrar la reserva en la base de datos. Por favor intenta nuevamente más tarde.';
=======
            return $this->respond(
                'No pude registrar la reserva en la base de datos. Por favor intenta nuevamente más tarde.',
                $this->defaultSuggestions
            );
>>>>>>> theirs
        }

        $notifMessage = sprintf('Nueva reserva pendiente de %s (%s)', $data['Name'], $data['Email']);
        guest_portal_record_notification($this->conn, 'admin', 'Reserva solicitada', $notifMessage, 'admin/roombook.php', $reservationId, null);
        guest_portal_record_notification($this->conn, 'recepcion', 'Reserva solicitada', $notifMessage, 'admin/roombook.php', $reservationId, null);

        $summary = sprintf(
            'Reserva registrada para %s (%s). Tipo: %s · Plan: %s · Llegada %s · Salida %s. Nuestro equipo confirmará la disponibilidad y tarifas en breve.',
            $data['Name'],
            $data['Email'],
            $data['RoomType'],
<<<<<<< ours
            $data['Meal'],
=======
            $this->friendlyMealLabel($data['Meal']),
>>>>>>> theirs
            $this->formatDateForUser($data['cin']),
            $this->formatDateForUser($data['cout'])
        );

<<<<<<< ours
        return $summary;
    }

    private function reservationStatusMessage(): string
    {
        if (!$this->userId) {
            return 'Para consultar el estado de tus reservas inicia sesión con tu cuenta y dirígete a la sección “Mis reservas” en el portal de huéspedes.';
        }

        if (!$this->activeReservation) {
            return 'No encuentro reservas activas asociadas a tu cuenta en este momento. Puedes iniciar una nueva reserva cuando lo desees.';
=======
        return $this->respond($summary, $this->defaultSuggestions);
    }

    private function reservationStatusMessage(): array
    {
        if (!$this->userId) {
            return $this->respond(
                'Para consultar el estado de tus reservas inicia sesión con tu cuenta y dirígete a la sección “Mis reservas” en el portal de huéspedes.',
                $this->defaultSuggestions
            );
        }

        if (!$this->activeReservation) {
            return $this->respond(
                'No encuentro reservas activas asociadas a tu cuenta en este momento. Puedes iniciar una nueva reserva cuando lo desees.',
                $this->defaultSuggestions
            );
>>>>>>> theirs
        }

        $status = $this->activeReservation['stat'] ?? 'NotConfirm';
        $label = match ($status) {
            'Confirm' => 'confirmada',
            'Ocupado', 'CheckIn' => 'en curso',
<<<<<<< ours
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
=======
            'Checkout' => 'finalizada',
            default => 'pendiente de confirmación',
        };

        $message = sprintf(
            'Tu reserva más reciente está %s. Llegada: %s · Salida: %s. Si deseas realizar algún ajuste o solicitar un servicio adicional, házmelo saber.',
            $label,
            $this->formatDateForUser((string) ($this->activeReservation['cin'] ?? '')),
            $this->formatDateForUser((string) ($this->activeReservation['cout'] ?? ''))
        );

        return $this->respond($message, $this->defaultSuggestions);
    }

    private function startServiceRequest(): array
    {
        if (!$this->userId || !$this->activeReservation) {
            return $this->respond(
                'Para solicitar algo a tu habitación inicia sesión y asegúrate de tener una reserva confirmada o en curso. También puedo ayudarte a crear una reserva nueva.',
                $this->defaultSuggestions
            );
        }

        $_SESSION[self::SERVICE_SESSION_KEY] = [
            'step' => 'type',
            'data' => [
                'reservation_id' => (int) ($this->activeReservation['id'] ?? 0),
                'guest_name' => (string) ($this->activeReservation['Name'] ?? ''),
            ],
        ];

        return $this->respond(
            '¿Qué deseas solicitar a tu habitación? Elige una opción o cuéntame con tus palabras.',
            $this->serviceTypeSuggestions(),
            ['placeholder' => 'Selecciona una opción o descríbela']
        );
    }

    private function continueServiceRequest(string $message): array
    {
        $state = $_SESSION[self::SERVICE_SESSION_KEY] ?? ['step' => 'type', 'data' => []];
        $step = $state['step'] ?? 'type';
        $data = $state['data'] ?? [];

        switch ($step) {
            case 'type':
                $type = $this->matchOption($message, $this->serviceTypeKeywords);
                if ($type === null || !isset($this->serviceTypeLabels[$type])) {
                    return $this->respond(
                        'Selecciona una de las opciones disponibles o descríbeme qué necesitas.',
                        $this->serviceTypeSuggestions(),
                        ['placeholder' => 'Selecciona una opción o descríbela']
                    );
                }
                $data['type'] = $type;
                $data['type_label'] = $this->serviceTypeLabels[$type];
                $nextPrompt = 'Perfecto, ¿qué deseas que enviemos o hagamos?';
                $nextStep = 'detail';
                $suggestions = $this->detailSuggestionsForType($type);
                $meta = ['placeholder' => 'Describe tu solicitud'];
                break;

            case 'detail':
                $detail = trim($message);
                if ($detail === '') {
                    return $this->respond(
                        'Cuéntame brevemente qué necesitas para poder registrarlo.',
                        $this->detailSuggestionsForType((string) ($data['type'] ?? '')),
                        ['placeholder' => 'Describe tu solicitud']
                    );
                }
                $data['detail'] = $detail;
                $typeLabel = (string) ($data['type_label'] ?? 'servicio');
                $nextPrompt = sprintf('Registraré %s con la nota: “%s”. ¿Lo confirmamos?', strtolower($typeLabel), $detail);
                $nextStep = 'confirm';
                $suggestions = [
                    $this->suggestion('Confirmar solicitud', 'Confirmar solicitud'),
                    $this->suggestion('Cambiar detalle', 'Cambiar detalle'),
                    $this->suggestion('Cancelar solicitud', 'Cancelar solicitud'),
                ];
                $meta = ['placeholder' => 'Confirma, cambia el detalle o cancela'];
                break;

            case 'confirm':
                $normalized = mb_strtolower($message, 'UTF-8');
                if ($this->contains($normalized, ['confirmar', 'enviar', 'sí', 'si', 'listo', 'ok'])) {
                    $this->resetService();
                    return $this->finalizeServiceRequest($data);
                }
                if ($this->contains($normalized, ['cambiar', 'editar', 'modificar'])) {
                    $_SESSION[self::SERVICE_SESSION_KEY] = [
                        'step' => 'detail',
                        'data' => $data,
                    ];
                    return $this->respond(
                        'Sin problema, ¿qué deseas solicitar exactamente?',
                        $this->detailSuggestionsForType((string) ($data['type'] ?? '')),
                        ['placeholder' => 'Actualiza tu solicitud']
                    );
                }
                if ($this->contains($normalized, ['cancelar', 'no enviar', 'ninguno'])) {
                    $this->resetService();
                    return $this->respond(
                        'Cancelé la solicitud. Si necesitas otra cosa, házmelo saber.',
                        $this->defaultSuggestions
                    );
                }
                return $this->respond(
                    '¿Deseas que envíe la solicitud ahora? Puedes confirmar, cambiar el detalle o cancelar.',
                    [
                        $this->suggestion('Confirmar solicitud', 'Confirmar solicitud'),
                        $this->suggestion('Cambiar detalle', 'Cambiar detalle'),
                        $this->suggestion('Cancelar solicitud', 'Cancelar solicitud'),
                    ],
                    ['placeholder' => 'Confirma, cambia o cancela la solicitud']
                );

            default:
                $this->resetService();
                return $this->respond(
                    'Ocurrió algo inesperado con la solicitud. Podemos intentarlo de nuevo cuando lo desees.',
                    $this->defaultSuggestions
                );
        }

        $_SESSION[self::SERVICE_SESSION_KEY] = [
            'step' => $nextStep,
            'data' => $data,
        ];

        return $this->respond($nextPrompt, $suggestions ?? [], $meta ?? []);
    }

    private function finalizeServiceRequest(array $data): array
    {
        $reservationId = (int) ($data['reservation_id'] ?? 0);
        $type = (string) ($data['type'] ?? '');
        $detail = trim((string) ($data['detail'] ?? ''));
        if ($reservationId <= 0 || $type === '' || $detail === '') {
            return $this->respond(
                'No pude registrar la solicitud porque faltan datos. Intenta nuevamente o contáctanos por recepción.',
                $this->defaultSuggestions
            );
        }

        $requestId = guest_portal_create_request(
            $this->conn,
            $reservationId,
            $this->userId,
            $type,
            $detail,
            'guest'
        );

        if (!$requestId) {
            return $this->respond(
                'No fue posible registrar la solicitud en este momento. Intenta de nuevo en unos minutos o comunícate con recepción.',
                $this->defaultSuggestions
            );
        }

        $guestName = (string) ($data['guest_name'] ?? ($this->activeReservation['Name'] ?? 'Huésped'));
        $typeLabel = $this->serviceTypeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type));
        $notifMessage = sprintf('%s solicitó %s: %s', $guestName, mb_strtolower($typeLabel, 'UTF-8'), $detail);

        guest_portal_record_notification($this->conn, 'admin', 'Solicitud de habitación', $notifMessage, 'admin/guest-requests.php', $reservationId, $requestId);
        guest_portal_record_notification($this->conn, 'recepcion', 'Solicitud de habitación', $notifMessage, 'admin/guest-requests.php', $reservationId, $requestId);

        $summary = sprintf('Avisé al equipo para atender tu solicitud de %s. Te notificaremos cuando esté en camino.', strtolower($typeLabel));

        return $this->respond($summary, $this->defaultSuggestions);
    }

    private function isBookingActive(): bool
    {
        return isset($_SESSION[self::BOOKING_SESSION_KEY]) && is_array($_SESSION[self::BOOKING_SESSION_KEY]);
>>>>>>> theirs
    }

    private function resetBooking(): void
    {
<<<<<<< ours
        unset($_SESSION[self::SESSION_KEY]);
=======
        unset($_SESSION[self::BOOKING_SESSION_KEY]);
    }

    private function isServiceActive(): bool
    {
        return isset($_SESSION[self::SERVICE_SESSION_KEY]) && is_array($_SESSION[self::SERVICE_SESSION_KEY]);
    }

    private function resetService(): void
    {
        unset($_SESSION[self::SERVICE_SESSION_KEY]);
>>>>>>> theirs
    }

    private function isResetCommand(string $normalized): bool
    {
        return $this->contains($normalized, ['reiniciar chat', 'reinicia', 'reset', 'comenzar de nuevo']);
    }

    private function isCancelling(string $normalized): bool
    {
<<<<<<< ours
        return $this->contains($normalized, ['cancelar', 'detener', 'no continuar', 'parar reserva']);
=======
        return $this->contains($normalized, ['cancelar', 'detener', 'no continuar', 'parar reserva', 'cancelar solicitud']);
>>>>>>> theirs
    }

    private function shouldStartBooking(string $normalized): bool
    {
        if ($this->contains($normalized, ['mi reserva', 'estado de mi reserva', 'tengo una reserva'])) {
            return false;
        }
<<<<<<< ours
        return $this->contains($normalized, ['reservar', 'hacer una reserva', 'quiero reservar', 'book', 'necesito una habitación', 'necesito habitacion']);
=======
        return $this->contains($normalized, ['reservar', 'reservar habitación', 'reservar habitacion', 'hacer una reserva', 'quiero reservar', 'necesito una habitación', 'necesito habitacion']);
    }

    private function shouldStartServiceRequest(string $normalized): bool
    {
        return $this->contains($normalized, [
            'servicio a la habitación',
            'servicio a la habitacion',
            'room service',
            'pedir a la habitación',
            'pedir a la habitacion',
            'enviar a mi habitación',
            'enviar a mi habitacion',
            'toalla adicional',
            'solicitud a la habitación',
            'solicitar a la habitacion',
        ]);
>>>>>>> theirs
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
<<<<<<< ours
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
=======
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if (is_array($user)) {
                $id = $user['id'] ?? $user['UserID'] ?? $user['UserId'] ?? null;
                if (is_numeric($id)) {
                    return (int) $id;
                }
            }
        }

        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }

        if (isset($_SESSION['uid']) && is_numeric($_SESSION['uid'])) {
            return (int) $_SESSION['uid'];
        }

        if (!empty($_SESSION['usermail']) && is_string($_SESSION['usermail'])) {
            $user = guest_portal_fetch_user($this->conn, $_SESSION['usermail']);
            if ($user) {
                $id = $user['UserID'] ?? $user['id'] ?? null;
                if (is_numeric($id)) {
                    return (int) $id;
                }
            }
        }

        return null;
    }

    /** @return array<int, array{label:string,value:string}> */
    private function buildDefaultSuggestions(): array
    {
        $items = [
            ['Reservar habitación', 'Reservar habitación'],
            ['Solicitar servicio a la habitación', 'Solicitar servicio a la habitación'],
            ['Ver estado de mi reserva', 'Estado de mi reserva'],
            ['Ver servicios del hotel', 'Servicios del hotel'],
        ];

        $suggestions = [];
        foreach ($items as $item) {
            $suggestions[] = $this->suggestion($item[0], $item[1]);
        }
        return $suggestions;
    }

    private function suggestion(string $label, string $value): array
    {
        return ['label' => $label, 'value' => $value];
    }

    /** @param array<int, string> $values */
    private function suggestionListFromValues(array $values): array
    {
        $unique = array_values(array_unique($values));
        return array_map(fn(string $item) => $this->suggestion($item, $item), $unique);
    }

    /** @return array<int, array{label:string,value:string}> */
    private function serviceTypeSuggestions(): array
    {
        $list = [];
        foreach ($this->serviceTypeLabels as $type => $label) {
            $list[] = $this->suggestion($label, $label);
        }
        return $list;
    }

    /** @return array<int, array{label:string,value:string}> */
    private function detailSuggestionsForType(string $type): array
    {
        return match ($type) {
            'room_service' => [
                $this->suggestion('Desayuno a la habitación', 'Desayuno a la habitación'),
                $this->suggestion('Café y bocadillos', 'Café y bocadillos'),
                $this->suggestion('Agua embotellada', 'Agua embotellada'),
                $this->suggestion('Postre nocturno', 'Postre nocturno'),
            ],
            'toalla' => [
                $this->suggestion('Toallas adicionales', 'Toallas adicionales'),
                $this->suggestion('Toallas para la piscina', 'Toallas para la piscina'),
                $this->suggestion('Reemplazar toallas usadas', 'Reemplazar toallas usadas'),
            ],
            'jabon' => [
                $this->suggestion('Kit de aseo personal', 'Kit de aseo personal'),
                $this->suggestion('Jabón y shampoo', 'Jabón y shampoo'),
                $this->suggestion('Cepillo de dientes', 'Cepillo de dientes'),
            ],
            'asistencia' => [
                $this->suggestion('Hablar con recepción', 'Hablar con recepción'),
                $this->suggestion('Ayuda con el televisor', 'Ayuda con el televisor'),
                $this->suggestion('Información sobre transporte', 'Información sobre transporte'),
            ],
            'minibar' => [
                $this->suggestion('Reabastecer minibar', 'Reabastecer minibar'),
                $this->suggestion('Registrar consumo del minibar', 'Registrar consumo del minibar'),
                $this->suggestion('Retirar artículos del minibar', 'Retirar artículos del minibar'),
            ],
            default => [
                $this->suggestion('Almohada adicional', 'Almohada adicional'),
                $this->suggestion('Limpieza adicional', 'Limpieza adicional'),
                $this->suggestion('Otro servicio', 'Otro servicio'),
            ],
        };
    }

    private function friendlyMealLabel(string $meal): string
    {
        return match ($meal) {
            'Room only' => 'Solo habitación',
            'Breakfast' => 'Desayuno incluido',
            'Half Board' => 'Desayuno y cena',
            'Full Board' => 'Pensión completa',
            default => $meal,
        };
    }

    private function defaultMeta(): array
    {
        return [
            'inputType' => 'text',
            'placeholder' => 'Escribe tu mensaje o elige una opción',
        ];
    }

    private function respond(string $message, array $suggestions = [], array $meta = []): array
    {
        $cleanSuggestions = [];
        foreach ($suggestions as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (!isset($item['label'], $item['value'])) {
                continue;
            }
            $label = (string) $item['label'];
            $value = (string) $item['value'];
            if ($label === '' || $value === '') {
                continue;
            }
            $cleanSuggestions[] = ['label' => $label, 'value' => $value];
        }

        $meta = array_merge($this->defaultMeta(), $meta);

        return [
            'message' => $message,
            'suggestions' => $cleanSuggestions,
            'meta' => $meta,
        ];
    }
}

>>>>>>> theirs
