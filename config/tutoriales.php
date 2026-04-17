<?php

return [
    'videos' => [
        'registro' => [
            'title' => 'Registro de Usuario',
            'filename' => 'tutorial_registro.mp4',
            'description' => 'Aprende a registrarte en la plataforma PickleTorneos',
            'category' => 'introduccion',
            'order' => 1,
        ],
        'inicio-sesion' => [
            'title' => 'Inicio de Sesión y Configuración Inicial',
            'filename' => 'tutorial_inicio_sesion_configuracion_inicial.mp4',
            'description' => 'Cómo iniciar sesión y comienza a configurar tu cuenta',
            'category' => 'introduccion',
            'order' => 2,
        ],
        'crear-torneo' => [
            'title' => 'Crear un Torneo',
            'filename' => 'tutoria_crear_torneo.mp4',
            'description' => 'Guía completa para crear tu primer torneo paso a paso',
            'category' => 'torneos',
            'order' => 3,
        ],
        'finalizar-torneo' => [
            'title' => 'Finalizar un Torneo',
            'filename' => 'tutoria_finalizar_torneo.mp4',
            'description' => 'Cómo completar y finalizar un torneo correctamente',
            'category' => 'torneos',
            'order' => 4,
        ],
        'referidos-sugerencias' => [
            'title' => 'Sistema de Referidos y Sugerencias',
            'filename' => 'tutorial_referidos_y_sugerencias.mp4',
            'description' => 'Aprende a ganar torneos gratis invitando amigos y cómo enviar sugerencias',
            'category' => 'crecimiento',
            'order' => 5,
        ],
    ],

    'categorias' => [
        'introduccion' => [
            'nombre' => 'Introducción',
            'icono' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'blue',
            'order' => 1,
        ],
        'torneos' => [
            'nombre' => 'Gestión de Torneos',
            'icono' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'color' => 'indigo',
            'order' => 2,
        ],
        'crecimiento' => [
            'nombre' => 'Crecimiento',
            'icono' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'green',
            'order' => 3,
        ],
    ],
];
