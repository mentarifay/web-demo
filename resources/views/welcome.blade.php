<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Pertamina Gas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
        }
        
        .pertamina-red-gradient {
            background: linear-gradient(135deg, #D71920 0%, #A01318 100%);
        }
        
        /* Animasi fade in dari kanan */
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Animasi fade in dari kiri */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .fade-in-right {
            animation: fadeInRight 1s ease-out forwards;
        }
        
        .fade-in-left {
            animation: fadeInLeft 1s ease-out forwards;
        }
        
        .delay-1 {
            animation-delay: 0.2s;
            opacity: 0;
        }
        
        .delay-2 {
            animation-delay: 0.4s;
            opacity: 0;
        }
        
        .delay-3 {
            animation-delay: 0.6s;
            opacity: 0;
        }
        
        /* Image overlay gradient */
        .image-overlay {
            background: linear-gradient(to right, rgba(0,0,0,0.2) 0%, rgba(215, 25, 32, 0.3) 100%);
        }
        
        /* Button hover effect */
        .btn-glow {
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(215, 25, 32, 0.2);
        }
        
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(215, 25, 32, 0.4);
        }

        /* Container dengan border radius */
        .main-container {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="overflow-hidden flex items-center justify-center min-h-screen p-6">
    
    <!-- Main Container dengan border radius -->
    <div class="main-container max-w-7xl w-full flex" style="height: 85vh;">
        
        <!-- Left Side - Image -->
        <div class="w-1/2 relative overflow-hidden">
            <!-- Background Image Site Visit -->
            <div class="absolute inset-0 bg-cover bg-center" 
                 style="background-image: url('{{ asset('images/visit.jpeg') }}');">
            </div>
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 image-overlay"></div>
            
            <!-- Logo di pojok kiri atas -->
            <div class="absolute top-8 left-8 fade-in-left z-10">
                @if(file_exists(public_path('images/logoptmm.png')))
                    <img src="{{ asset('images/logoptmm.png') }}" alt="Pertamina Gas Logo" class="h-16 w-auto object-contain drop-shadow-xl">
                @elseif(file_exists(public_path('images/logoptmm.png')))
                    <img src="{{ asset('images/logoptmm.png') }}" alt="Pertamina Gas Logo" class="h-16 w-auto object-contain drop-shadow-xl">
                @else
                    <div class="bg-white px-4 py-2 rounded-lg shadow-lg">
                        <span class="text-red-600 font-bold text-lg">PERTAMINA GAS</span>
                    </div>
                @endif
            </div>
            
            <!-- Text di atas gambar -->
            <div class="absolute bottom-12 left-8 z-10 fade-in-left delay-1">
                <h2 class="text-white text-3xl font-semibold mb-2 drop-shadow-lg">Gas Field</h2>
                <p class="text-white text-base opacity-90 drop-shadow">Site Visit Experience</p>
            </div>
        </div>
        
        <!-- Right Side - Content -->
        <div class="w-full md:w-1/2 pertamina-red-gradient flex items-center justify-center p-12 relative">
            
            <!-- Content Card -->
            <div class="max-w-md w-full fade-in-right delay-2">
                
                <!-- Mobile Logo (hanya muncul di mobile) -->
                <div class="md:hidden mb-8 text-center">
                    @if(file_exists(public_path('images/logoptmm.png')))
                        <img src="{{ asset('images/logoptmm.png') }}" alt="Pertamina Gas Logo" class="h-20 w-auto object-contain mx-auto drop-shadow-2xl">
                    @elseif(file_exists(public_path('images/logoptmm.png')))
                        <img src="{{ asset('images/logoptmm.png') }}" alt="Pertamina Gas Logo" class="h-20 w-auto object-contain mx-auto drop-shadow-2xl">
                    @else
                        <div class="bg-white px-6 py-3 rounded-xl shadow-lg inline-block">
                            <span class="text-red-600 font-bold text-2xl">PERTAMINA GAS</span>
                        </div>
                    @endif
                </div>
                
                <!-- Title -->
                <h1 class="text-white text-4xl font-semibold mb-3 tracking-tight">
                    Pertamina Gas
                </h1>
                
                <!-- Subtitle -->
                <p class="text-white text-lg font-normal mb-2 opacity-95">
                    Dashboard Penyaluran Gas
                </p>
                
                <p class="text-white text-base font-light mb-10 opacity-85">
                    Monitoring Data 2020-2025
                </p>
                
                <!-- CTA Button -->
                <a href="{{ route('dashboard') }}" class="btn-glow block w-full bg-white text-red-600 px-8 py-4 rounded-xl text-base font-semibold hover:bg-gray-50 transition text-center">
                    <span>Masuk Dashboard</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
                
                <!-- Features List -->
                <div class="mt-8 space-y-3">
                    <div class="flex items-center text-white">
                        <i class="fas fa-check-circle mr-3 text-green-300 text-sm"></i>
                        <span class="text-sm opacity-90 font-light">Real-time monitoring & analytics</span>
                    </div>
                    <div class="flex items-center text-white">
                        <i class="fas fa-check-circle mr-3 text-green-300 text-sm"></i>
                        <span class="text-sm opacity-90 font-light">Data lengkap 2020-2025</span>
                    </div>
                    <div class="flex items-center text-white">
                        <i class="fas fa-check-circle mr-3 text-green-300 text-sm"></i>
                        <span class="text-sm opacity-90 font-light">Filter berdasarkan shipper & periode</span>
                    </div>
                </div>
                
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-6 left-0 right-0 text-center">
                <p class="text-white text-xs opacity-50 font-light">
                    © 2025 Pertamina Gas. All rights reserved.
                </p>
            </div>
            
        </div>
        
    </div>
    
</body>
</html>