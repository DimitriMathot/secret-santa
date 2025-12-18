<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre Secret Santa - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-red-50 to-green-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="bg-white shadow-xl rounded-2xl p-8 text-center">
            <div class="mb-8">
                <div class="text-6xl mb-4">🎅</div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Bonjour {{ $participant->name }} !</h1>
                <p class="text-gray-600">Événement: <strong>{{ $participant->event->name }}</strong></p>
            </div>

            @if($participant->assignment && $assignedParticipant)
                <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl p-8 mb-6 shadow-lg">
                    <div class="text-4xl mb-4">🎁</div>
                    <h2 class="text-2xl font-bold mb-4">Votre Secret Santa est...</h2>
                    <div class="bg-white text-red-600 rounded-lg p-6 mb-4">
                        <div class="text-3xl font-bold">{{ $assignedParticipant->name }}</div>
                        <div class="text-lg text-gray-600 mt-2">{{ $assignedParticipant->email }}</div>
                    </div>
                    <p class="text-sm opacity-90">Préparez un beau cadeau pour cette personne ! 🎄</p>
                </div>

                @if($participant->has_viewed_assignment && $participant->viewed_at)
                    <p class="text-sm text-gray-500">
                        Vous avez consulté cette assignation le {{ $participant->viewed_at->format('d/m/Y à H:i') }}
                    </p>
                @endif
            @elseif($participant->event->assignments_generated)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg">
                    <p class="font-semibold">⚠️ Aucune assignation trouvée</p>
                    <p class="text-sm mt-2">Veuillez contacter l'administrateur de l'événement.</p>
                </div>
            @else
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-lg">
                    <p class="font-semibold">⏳ Les assignations n'ont pas encore été générées</p>
                    <p class="text-sm mt-2">Vous recevrez un email lorsque les assignations seront prêtes.</p>
                </div>
            @endif

            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    Ce lien est unique et confidentiel. Ne le partagez avec personne.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

