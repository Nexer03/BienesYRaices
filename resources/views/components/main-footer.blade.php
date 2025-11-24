<footer class="bg-white text-gray-700 pt-12 pb-8 mt-auto border-t border-gray-200 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-800">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10">
        {{-- Marca y descripción --}}
        <div class="md:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-home text-blue-500 text-xl"></i>
                <span class="text-gray-900 font-extrabold text-2xl tracking-tight dark:text-white">
                    SIN BECA <span class="text-gray-700 font-bold dark:text-gray-200">NO HAY RENTA</span>
                </span>
            </div>
            <p class="text-gray-600 leading-relaxed mb-5 text-sm md:text-base dark:text-gray-400">
                Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades con sus futuros dueños de manera eficiente y profesional.
            </p>
            <div class="flex space-x-4 text-lg text-gray-500 dark:text-gray-400">
                 <a href="https://www.facebook.com/profile.php?id=61584076357326"
       target="_blank"
       rel="noopener noreferrer"
       class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
        <i class="fab fa-facebook-f"></i>
    </a>
                <a href="#" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors"><i class="fab fa-x-twitter"></i></a>
            </div>
        </div>

        {{-- Navegación --}}
        <div>
            <h3 class="text-gray-900 text-lg font-semibold mb-4 border-b border-gray-200 pb-2 dark:text-white dark:border-gray-700">
                Navegación
            </h3>
            <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-300">
                <li><a href="{{ route('visits.my') }}" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Mis Visitas</a></li>
                <li><a href="{{ route('properties.map') }}" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Mapa</a></li>
                <li><a href="{{ route('agent.home') }}" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Panel de Agente</a></li>
                <li><a href="{{ route('agent.view') }}" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Modo Vendedor</a></li>
            </ul>
        </div>

        {{-- Contacto --}}
        <div>
            <h3 class="text-gray-900 text-lg font-semibold mb-4 border-b border-gray-200 pb-2 dark:text-white dark:border-gray-700">
                Contacto
            </h3>
            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                <li class="flex items-center">
                    <i class="fas fa-envelope text-blue-500 mr-2"></i>
                    ti1901032@academica.utbb.edu.mx
                </li>
                <li class="flex items-center">
                    <i class="fas fa-phone text-blue-500 mr-2"></i>
                    +52 (322) 108-8514
                </li>
                <li class="flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                    Universidad Tecnologica de Bahia de Banderas, Mexico
                </li>
            </ul>
        </div>
    </div>

    {{-- Línea inferior --}}
    <div class="border-t border-gray-200 mt-10 pt-6 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-500">
        <div class="flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto px-6">
            <p>© {{ date('Y') }} SIN BECA NO HAY RENTA. </p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <button type="button" data-modal-target="privacy-modal" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Privacidad</button>
                <button type="button" data-modal-target="terms-modal" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Términos</button>
                <button type="button" data-modal-target="cookies-modal" class="hover:text-blue-500 dark:hover:text-blue-400 transition-colors">Cookies</button>
            </div>
        </div>
    </div>

    {{-- Modal: Privacidad --}}
    <div id="privacy-modal" data-modal class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-8 hidden z-50" aria-hidden="true">
        <div class="bg-white text-gray-900 rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden border border-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800">
            <div class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-500">Información legal</p>
                    <h2 class="text-2xl font-semibold">Política de Privacidad</h2>
                </div>
                <button type="button" data-modal-close class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto text-sm leading-relaxed text-gray-700 dark:text-gray-200">
               <p>
                    En SIN BECA NO HAY RENTA valoramos tu privacidad y tratamos tus datos personales 
                    con responsabilidad. La información que recopilamos se utiliza exclusivamente 
                    para brindarte una experiencia segura, eficiente y personalizada dentro de la 
                    plataforma. Esto incluye la administración de tu cuenta, la gestión de 
                    propiedades, visitas, reservas y la comunicación necesaria entre usuarios, 
                    agentes y propietarios.
                </p>
                <p>
                    No compartimos tu información con terceros, salvo cuando sea estrictamente 
                    necesario para cumplir con obligaciones legales o cuando tú lo autorices de 
                    manera explícita. Todos los datos son procesados bajo medidas técnicas y 
                    organizativas destinadas a proteger su confidencialidad, integridad y 
                    disponibilidad.
                </p>
                <p>
                    Puedes solicitar el acceso, corrección o eliminación de tus datos personales en 
                    cualquier momento desde tu cuenta o escribiendo directamente a 
                    <strong>ti1901032@academica.utbb.edu.mx</strong>. Nuestro compromiso es mantener 
                    tus datos seguros y brindarte total transparencia sobre su uso.
                </p>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" data-modal-close class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: Términos --}}
    <div id="terms-modal" data-modal class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-8 hidden z-50" aria-hidden="true">
        <div class="bg-white text-gray-900 rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden border border-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800">
            <div class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-500">Información legal</p>
                    <h2 class="text-2xl font-semibold">Términos y Condiciones</h2>
                </div>
                <button type="button" data-modal-close class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto text-sm leading-relaxed text-gray-700 dark:text-gray-200">
              <p>
                Al utilizar SIN BECA NO HAY RENTA aceptas nuestros lineamientos de uso, 
                publicación y conducta dentro de la plataforma. Esperamos que todos los usuarios 
                actúen con responsabilidad y de buena fe, evitando cualquier intento de fraude, 
                suplantación de identidad, manipulación de información o abuso hacia otros 
                usuarios o agentes.
            </p>
            <p>
                Aunque trabajamos constantemente para ofrecer una plataforma estable y segura, 
                los servicios se proporcionan “tal cual”, por lo que no garantizamos la 
                disponibilidad continua del sistema ni la ausencia de interrupciones, fallos o 
                errores causados por factores externos, proveedores de servicios o situaciones 
                fuera de nuestro control.
            </p>
            <p>
                Nos reservamos el derecho de suspender cuentas, remover contenido o intervenir 
                en casos donde se detecten actividades irregulares o incumplimiento de estos 
                términos. El uso del sitio y cualquier disputa relacionada se regirán según la 
                legislación mexicana aplicable. Para asistencia adicional puedes contactarnos en 
                <strong>ti1901032@academica.utbb.edu.mx</strong>.
            </p>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" data-modal-close class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal: Cookies --}}
    <div id="cookies-modal" data-modal class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-8 hidden z-50" aria-hidden="true">
        <div class="bg-white text-gray-900 rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden border border-gray-200 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-800">
            <div class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-gray-800">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-500">Información legal</p>
                    <h2 class="text-2xl font-semibold">Política de Cookies</h2>
                </div>
                <button type="button" data-modal-close class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto text-sm leading-relaxed text-gray-700 dark:text-gray-200">
                <p>
                    Utilizamos cookies propias y de terceros para mejorar tu experiencia en la 
                    plataforma. Estas cookies nos permiten recordar tus preferencias, optimizar el 
                    rendimiento del sitio, analizar el uso general del sistema y ofrecerte 
                    contenido más relevante según tu actividad.
                </p>
                <p>
                    Algunas cookies son esenciales para el funcionamiento del sitio, mientras que 
                    otras pueden ser desactivadas desde la configuración de tu navegador. Ten en 
                    cuenta que deshabilitar ciertas cookies podría limitar funcionalidades como 
                    guardado de preferencias, navegación personalizada o acceso rápido a ciertos 
                    módulos.
                </p>
                <p>
                    Al continuar navegando en SIN BECA NO HAY RENTA aceptas el uso de cookies bajo 
                    esta política. Si deseas más información o necesitas asistencia para ajustar tu 
                    consentimiento, puedes comunicarte con nosotros al correo 
                    <strong>ti1901032@academica.utbb.edu.mx</strong>.
                </p>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" data-modal-close class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleScroll = (lock) => {
                document.body.classList.toggle('overflow-hidden', lock);
            };

            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                toggleScroll(true);
            };

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                toggleScroll(false);
            };

            document.querySelectorAll('[data-modal-target]').forEach((button) => {
                button.addEventListener('click', () => {
                    const modal = document.getElementById(button.dataset.modalTarget);
                    if (modal) {
                        openModal(modal);
                    }
                });
            });

            document.querySelectorAll('[data-modal]').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal(modal);
                    }
                });
            });

            document.querySelectorAll('[data-modal-close]').forEach((button) => {
                button.addEventListener('click', () => {
                    const modal = button.closest('[data-modal]');
                    if (modal) {
                        closeModal(modal);
                    }
                });
            });
        });
    </script>
</footer>
