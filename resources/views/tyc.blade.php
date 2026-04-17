@extends('layouts.app')

@section('title', 'Términos y Condiciones')

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-brand-600 via-purple-600 to-brand-800 text-white overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-1/2 translate-y-1/2"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 bottom-10">
            <div class="text-center">
                <!-- Logo -->
                <div class="mb-6">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-16 sm:h-20 mx-auto">
                </div>

                <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-4">
                    Términos y Condiciones
                </h1>
                <p class="text-lg sm:text-xl text-brand-100">
                    Última actualización: 13 de octubre de 2025
                </p>
            </div>
        </div>

        <!-- Wave Separator -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z" fill="#FFFFFF"/>
            </svg>
        </div>
    </section>

    <!-- Content Section -->
    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Introducción -->
            <div class="prose prose-lg max-w-none mb-8">
                <p class="text-gray-700 leading-relaxed">
                    Por favor, lea atentamente estos Términos y Condiciones de Uso antes de utilizar la plataforma web <strong>PickleTorneos</strong>. Si está en desacuerdo con alguna parte o en su totalidad con estos Términos y Condiciones, no debe hacer uso de la misma.
                </p>
                <p class="text-gray-700 leading-relaxed mt-4">
                    <strong>PickleTorneos</strong> es una plataforma web desarrollada para la gestión y seguimiento de torneos deportivos. Permite la creación de torneos y el seguimiento de cada etapa generada, así como la interacción entre organizadores y participantes.
                </p>
            </div>

            <!-- Secciones -->
            <div class="space-y-8">
                <!-- Sección 1 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-brand-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">1</span>
                        Objeto del Servicio
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">1.1. Objeto de la Plataforma</h3>
                            <p class="text-gray-700 mb-3"><strong>PickleTorneos</strong> es una herramienta software que facilita la gestión de torneos deportivos. Permite a los Usuarios:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                                <li>Crear torneos.</li>
                                <li>Hacer un seguimiento del avance de los mismos.</li>
                                <li>Consultar el estado y realizar modificaciones antes o durante su desarrollo.</li>
                                <li>Adjuntar archivos multimedia.</li>
                                <li>Mantener un registro de torneos finalizados.</li>
                                <li>Gestionar participantes, complejos y horarios.</li>
                                <li>Enviar y recibir notificaciones dentro de la plataforma.</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">1.2. Alcance del Servicio</h3>
                            <p class="text-gray-700"><strong>PickleTorneos</strong> se limita a proporcionar la plataforma de gestión. La responsabilidad sobre la información cargada o publicada, así como el cumplimiento de plazos y la veracidad de los datos, recae exclusivamente en la organización o usuario administrador y en los usuarios registrados.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección 2 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">2</span>
                        Uso de la Plataforma
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">2.1. Confidencialidad de Credenciales</h3>
                            <p class="text-gray-700">El Usuario es responsable de mantener la confidencialidad de sus credenciales de acceso (correo electrónico y contraseña) y de todas las actividades realizadas bajo su perfil. Cualquier uso no autorizado de su perfil o brecha de seguridad deberá ser notificada de inmediato a la organización administradora.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">2.2. Uso Adecuado</h3>
                            <p class="text-gray-700 mb-3">El Usuario se compromete a utilizar la Plataforma de manera lícita, ética y conforme a estos Términos y Condiciones y a la legislación aplicable. Queda prohibido:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                                <li>Usar la Plataforma con fines ilegales o fraudulentos.</li>
                                <li>Introducir código malicioso (virus, malware, etc.).</li>
                                <li>Dañar, sobrecargar o perjudicar los servidores o redes de <strong>PickleTorneos</strong>.</li>
                                <li>Intentar obtener acceso no autorizado a sistemas o datos.</li>
                                <li>Manipular la información de forma malintencionada.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sección 3 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-green-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">3</span>
                        Responsabilidad del Proveedor
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">3.1. Limitación de Responsabilidad</h3>
                            <p class="text-gray-700 mb-3">El proveedor de <strong>PickleTorneos</strong> se limita a la operación y mantenimiento técnico de la plataforma. No es responsable de:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                                <li>Pérdidas o daños derivados del uso o imposibilidad de uso de la Plataforma.</li>
                                <li>Errores, interrupciones, demoras, virus o fallas de red ajenas al Administrador.</li>
                                <li>La veracidad o calidad de la información cargada por usuarios u organizaciones.</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">3.2. Garantías</h3>
                            <p class="text-gray-700">El Administrador no otorga garantías expresas o implícitas sobre la disponibilidad o rendimiento ininterrumpido de la Plataforma.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">3.3. Seguridad de Red</h3>
                            <p class="text-gray-700">Se implementan medidas razonables de seguridad, pero el Usuario reconoce que toda transmisión de datos por Internet implica un riesgo inherente.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección 4 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-yellow-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">4</span>
                        Propiedad Intelectual
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">4.1. Propiedad de PickleTorneos</h3>
                            <p class="text-gray-700">Todos los derechos de propiedad intelectual sobre diseño, código fuente, gráficos, textos, bases de datos, marcas y logotipos son exclusivos del Administrador o sus licenciantes.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">4.2. Restricciones de Uso</h3>
                            <p class="text-gray-700">Queda prohibida cualquier reproducción, modificación, distribución o explotación del contenido sin autorización previa y por escrito.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">4.3. Infracciones</h3>
                            <p class="text-gray-700">Cualquier violación será sancionada según la <strong>Ley 11.723 de Propiedad Intelectual</strong> y la <strong>Ley 22.362 de Marcas</strong> de Argentina, además de normativa aplicable.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección 5 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-red-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">5</span>
                        Disponibilidad del Servicio
                    </h2>
                    <p class="text-gray-700">El Administrador se reserva el derecho de modificar, suspender o dar de baja la Plataforma en cualquier momento y sin previo aviso, por motivos técnicos, de seguridad, operativos o de fuerza mayor.</p>
                </div>

                <!-- Sección 6 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">6</span>
                        Errores y Mantenimiento
                    </h2>
                    <p class="text-gray-700">El Administrador no se hace responsable de errores, virus u otros componentes dañinos ajenos a la plataforma. Podrá realizar cambios y mejoras técnicas en cualquier momento.</p>
                </div>

                <!-- Sección 7 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-brand-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">7</span>
                        Violaciones a los Términos
                    </h2>
                    <p class="text-gray-700">El Administrador podrá investigar y sancionar violaciones a estos Términos, incluyendo suspensión o baja de acceso, eliminación de contenidos y denuncias ante autoridades competentes.</p>
                </div>

                <!-- Sección 8 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-purple-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">8</span>
                        Modificaciones
                    </h2>
                    <p class="text-gray-700">Estos Términos y Condiciones podrán ser modificados en cualquier momento. La versión actualizada será publicada en la Plataforma y entrará en vigencia desde su publicación. El uso continuado implica la aceptación de los cambios.</p>
                </div>

                <!-- Sección 9 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-green-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">9</span>
                        Protección de Datos Personales
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">9.1. Cumplimiento Normativo</h3>
                            <p class="text-gray-700">El Administrador cumple con la <strong>Ley N° 25.326 de Protección de Datos Personales de Argentina</strong>.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">9.2. Privacidad</h3>
                            <p class="text-gray-700">Los datos se almacenan con medidas técnicas y organizativas que garantizan seguridad y confidencialidad.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">9.3. Finalidad</h3>
                            <p class="text-gray-700">Los datos serán usados únicamente para la prestación y mejora de los servicios de la Plataforma.</p>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">9.4. Derechos del Usuario</h3>
                            <p class="text-gray-700">Los titulares podrán acceder, rectificar, actualizar o suprimir sus datos conforme a la normativa vigente.</p>
                        </div>
                    </div>
                </div>

                <!-- Sección 10 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-yellow-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">10</span>
                        Ley Aplicable y Jurisdicción
                    </h2>
                    <p class="text-gray-700">Estos Términos se regirán por las leyes de la <strong>República Argentina</strong>. Cualquier disputa será sometida a los tribunales ordinarios de la <strong>Ciudad de Santiago del Estero</strong>, renunciando a cualquier otro fuero.</p>
                </div>

                <!-- Sección 11 -->
                <div class="bg-gray-50 rounded-xl p-6 sm:p-8">
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <span class="flex-shrink-0 w-10 h-10 bg-red-600 text-white rounded-lg flex items-center justify-center text-lg font-bold">11</span>
                        Valoración
                    </h2>
                    <p class="text-gray-700">El Administrador pondrá a disposición encuestas de satisfacción para valorar la Plataforma, de manera anónima o mediante inicio de sesión.</p>
                </div>
            </div>

            <!-- Botón de regreso -->
            <div class="mt-12 text-center">
                <a href="{{ route('landing') }}"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-purple-600 text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-brand-700 hover:to-purple-700 transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Inicio
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <!-- Logo en footer -->
                <div class="mb-4">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-12 mx-auto">
                </div>
                <p class="text-gray-400 mb-4">Sistema profesional de gestión de torneos deportivos</p>
                <div class="flex justify-center gap-6 text-sm">
                    <a href="{{ route('tyc') }}" class="hover:text-white transition duration-200">Términos y Condiciones</a>
                </div>
                <p class="mt-6 text-sm text-gray-500">
                    © 2025 PickleTorneos. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>
@endsection
