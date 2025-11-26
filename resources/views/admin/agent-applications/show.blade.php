<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de solicitud de agente</title>

    <!-- Anti-flash -->
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' };
    </script>

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 p-6">

    <div class="max-w-4xl mx-auto">

        <!-- Título -->
        <header class="mb-8">
            <h1 class="text-2xl font-bold flex items-center gap-2">
                <i class="fa-solid fa-id-card-clip text-blue-500"></i>
                Detalle de solicitud de agente
            </h1>
        </header>

        <!-- Card -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl rounded-2xl p-6 sm:p-8 space-y-8">

            <!-- Información -->
            <section>
                <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-user text-blue-500"></i>
                    Información del solicitante
                </h2>

                <p><span class="font-medium">Nombre:</span> {{ $application->user->name }}</p>
                <p><span class="font-medium">Email:</span> {{ $application->user->email }}</p>
            </section>

            <!-- Documentación -->
            <section>
                <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-blue-500"></i>
                    Documentación
                </h2>

                <p><span class="font-medium">RFC:</span> {{ $application->rfc }}</p>
                <p><span class="font-medium">CURP:</span> {{ $application->curp }}</p>

                <!-- INE -->
                <div class="mt-5">
                    <h3 class="text-md font-semibold">Identificación (INE)</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">

                        <!-- Frontal -->
                        <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <p class="font-medium text-sm text-gray-600 dark:text-gray-300 mb-2">Frontal:</p>
                            <img src="{{ asset('storage/' . $application->ine_front) }}"
                                 alt="INE frontal"
                                 class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 shadow-sm object-cover">
                        </div>

                        <!-- Reverso -->
                        <div class="p-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <p class="font-medium text-sm text-gray-600 dark:text-gray-300 mb-2">Reverso:</p>
                            <img src="{{ asset('storage/' . $application->ine_back) }}"
                                 alt="INE reverso"
                                 class="block w-full rounded-lg border border-gray-300 dark:border-gray-700 shadow-sm object-cover">
                        </div>

                    </div>
                </div>
            </section>

            <!-- Estado -->
            <section class="space-y-2">
                <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    Estado
                </h2>

                <p class="flex items-center gap-2">
                    <span class="font-medium">Estado:</span>
                    @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-semibold border border-amber-200">Pendiente</span>
                    @elseif ($application->status === \App\Models\AgentApplication::STATUS_APPROVED)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold border border-emerald-200">Aprobada</span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold border border-red-200">Rechazada</span>
                    @endif
                </p>

                <p>
                    <span class="font-medium">Motivo rechazo:</span>
                    {{ $application->rejection_reason ?? '—' }}
                </p>
            </section>

            <!-- Acciones -->
            <section>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:gap-4 mt-4">

                    <!-- Volver -->
                    <a href="{{ route('admin.agent-applications.index') }}"
                       class="cursor-pointer px-5 py-2.5 bg-gray-200 dark:bg-gray-700 rounded-lg text-sm font-semibold
                              text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        Volver
                    </a>

                    @if ($application->status === \App\Models\AgentApplication::STATUS_PENDING)
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch w-full">
                            <!-- Aprobar -->
                            <form method="POST" action="{{ route('admin.agent-applications.approve', $application) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-5 py-2.5 bg-green-600 hover:bg-green-500 rounded-lg text-sm font-semibold text-white flex items-center justify-center gap-2 shadow">
                                    <i class="fa-solid fa-check"></i>
                                    Aprobar
                                </button>
                            </form>
                            <!-- Rechazar -->
                            <form method="POST" action="{{ route('admin.agent-applications.reject', $application) }}" class="flex-1 flex flex-col gap-2">
                                @csrf
                                <button type="submit" class="w-full px-5 py-2.5 bg-red-600 hover:bg-red-500 rounded-lg text-sm font-semibold text-white flex items-center justify-center gap-2 shadow">
                                    <i class="fa-solid fa-xmark"></i>
                                    Rechazar
                                </button>
                                <textarea name="rejection_reason" required placeholder="Motivo de rechazo" rows="2"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700
                                           bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-200 shadow-sm
                                           focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                            </form>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</body>
</html>
