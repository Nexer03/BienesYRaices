<footer class="bg-gray-900 text-gray-300 pt-12 pb-8 mt-auto border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10">
        {{-- Marca y descripción --}}
        <div class="md:col-span-2">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-home text-blue-500 text-xl"></i>
                <span class="text-white font-extrabold text-2xl tracking-tight">SIN BECA <span class="text-gray-200 font-bold">NO HAY RENTA</span></span>
            </div>
            <p class="text-gray-400 leading-relaxed mb-5 text-sm md:text-base">
                Tu plataforma confiable para la gestión inmobiliaria. Conectamos propiedades con sus futuros dueños de manera eficiente y profesional.
            </p>
            <div class="flex space-x-4 text-lg">
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-instagram"></i></a>
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="hover:text-blue-400 transition-colors"><i class="fab fa-x-twitter"></i></a>
            </div>
        </div>

        {{-- Navegación --}}
        <div>
            <h3 class="text-white text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Navegación</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('visits.my') }}" class="hover:text-blue-400 transition-colors">Mis Visitas</a></li>
                <li><a href="{{ route('properties.map') }}" class="hover:text-blue-400 transition-colors">Mapa</a></li>
                <li><a href="{{ route('agent.home') }}" class="hover:text-blue-400 transition-colors">Panel de Agente</a></li>
                <li><a href="{{ route('agent.view') }}" class="hover:text-blue-400 transition-colors">Modo Vendedor</a></li>
            </ul>
        </div>

        {{-- Contacto --}}
        <div>
            <h3 class="text-white text-lg font-semibold mb-4 border-b border-gray-700 pb-2">Contacto</h3>
            <ul class="space-y-3 text-sm">
                <li class="flex items-center"><i class="fas fa-envelope text-blue-400 mr-2"></i> soporte@sinbeca.com</li>
                <li class="flex items-center"><i class="fas fa-phone text-blue-400 mr-2"></i> +1 (555) 123-4567</li>
                <li class="flex items-center"><i class="fas fa-map-marker-alt text-blue-400 mr-2"></i> Ciudad, País</li>
            </ul>
        </div>
    </div>

    {{-- Línea inferior --}}
    <div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm text-gray-500">
        <div class="flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto px-6">
            <p>© {{ date('Y') }} SIN BECA NO HAY RENTA. Todos los derechos reservados.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <button type="button" data-modal-target="privacy-modal" class="hover:text-blue-400 transition-colors">Privacidad</button>
                <button type="button" data-modal-target="terms-modal" class="hover:text-blue-400 transition-colors">Términos</button>
                <button type="button" data-modal-target="cookies-modal" class="hover:text-blue-400 transition-colors">Cookies</button>
            </div>
        </div>
    </div>

    {{-- Modal: Privacidad --}}
    <div id="privacy-modal" data-modal class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-8 hidden z-50" aria-hidden="true">
        <div class="bg-gray-900 text-gray-100 rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden">
            <div class="flex items-start justify-between p-6 border-b border-gray-800">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-400">Información legal</p>
                    <h2 class="text-2xl font-semibold">Política de Privacidad</h2>
                </div>
                <button type="button" data-modal-close class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto text-sm leading-relaxed">
                <p>
                    Respetamos tu privacidad y protegemos tus datos personales. Solo recopilamos la información necesaria para ofrecer nuestros servicios y la tratamos con la máxima confidencialidad.
                </p>
                <p>
                    Tus datos se utilizan para crear y administrar tu cuenta, procesar operaciones inmobiliarias y brindarte soporte. No compartimos tu información con terceros sin tu consentimiento, salvo obligación legal.
                </p>
                <p>
                    Puedes acceder, actualizar o eliminar tus datos en cualquier momento desde tu perfil o escribiendo a soporte@sinbeca.com. Implementamos medidas de seguridad técnicas y organizativas para mantenerlos protegidos.
                </p>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" data-modal-close class="px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-100 font-medium">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- Modal: Términos --}}
    <div id="terms-modal" data-modal class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-8 hidden z-50" aria-hidden="true">
        <div class="bg-gray-900 text-gray-100 rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden">
            <div class="flex items-start justify-between p-6 border-b border-gray-800">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-400">Información legal</p>
                    <h2 class="text-2xl font-semibold">Términos y Condiciones</h2>
                </div>
                <button type="button" data-modal-close class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto text-sm leading-relaxed">
                <p>
                    Al usar la plataforma aceptas cumplir con nuestras políticas de publicación y conducta. Nos reservamos el derecho de moderar contenido y suspender cuentas que incumplan estos términos o intenten actividades fraudulentas.
                </p>
                <p>
                    El uso del sitio se ofrece "tal cual", sin garantías de disponibilidad continua. Nos esforzamos por mantener la plataforma estable y segura, pero no somos responsables por pérdidas derivadas de interrupciones o errores de terceros.
                </p>
                <p>
                    Cualquier disputa se resolverá conforme a la legislación local aplicable. Si tienes dudas, contáctanos para recibir atención personalizada.
                </p>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" data-modal-close class="px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-100 font-medium">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- Modal: Cookies --}}
    <div id="cookies-modal" data-modal class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center px-4 py-8 hidden z-50" aria-hidden="true">
        <div class="bg-gray-900 text-gray-100 rounded-xl shadow-2xl max-w-3xl w-full overflow-hidden">
            <div class="flex items-start justify-between p-6 border-b border-gray-800">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-400">Información legal</p>
                    <h2 class="text-2xl font-semibold">Política de Cookies</h2>
                </div>
                <button type="button" data-modal-close class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto text-sm leading-relaxed">
                <p>
                    Utilizamos cookies propias y de terceros para recordar tus preferencias, analizar el uso del sitio y mejorar tu experiencia de navegación.
                </p>
                <p>
                    Puedes configurar tu navegador para rechazar cookies o recibir avisos antes de guardarlas. Algunas funciones pueden verse afectadas si las desactivas.
                </p>
                <p>
                    Al continuar navegando aceptas el uso de cookies según esta política. Para más información o ajustar tu consentimiento, escríbenos a soporte@sinbeca.com.
                </p>
            </div>
            <div class="flex justify-end gap-3 px-6 pb-6">
                <button type="button" data-modal-close class="px-4 py-2 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-100 font-medium">Cerrar</button>
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
