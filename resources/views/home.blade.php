@extends('layouts.app')

@section('content')

<section class="h-screen w-full flex flex-col justify-center items-center relative overflow-hidden bg-white">
    
    <div class="text-center max-w-6xl mx-auto z-10 -mt-10 px-6">
        <h1 class="reveal text-6xl md:text-8xl lg:text-9xl font-bold leading-[1] mb-8 tracking-tighter">
            Your trusted <br> creative partner.
        </h1>
        <p class="reveal text-lg md:text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed font-medium">
            We deliver creative branding, web design, and UI/UX solutions to make the most impact for your business.
        </p>
        <div class="reveal flex gap-4 justify-center">
            <button class="px-8 py-4 rounded-full font-bold btn-invert btn-black hover-target flex items-center gap-2">
                Request a quote <span>👋</span>
            </button>
        </div>
    </div>

    <div class="marquee-container hover-target">
        <div class="marquee-content text-4xl md:text-5xl font-light text-gray-300 uppercase tracking-tighter">
            <span class="mx-8">/ STRATEGIC EXPERIENCES</span>
            <span class="mx-8 text-black font-medium">/ RESULTS DRIVEN SOLUTIONS</span>
            <span class="mx-8">/ BUSINESS VALUE</span>
            <span class="mx-8 text-black font-medium">/ PURPOSEFUL DESIGNS</span>
            <span class="mx-8">/ STRATEGIC EXPERIENCES</span>
            <span class="mx-8 text-black font-medium">/ RESULTS DRIVEN SOLUTIONS</span>
            <span class="mx-8">/ BUSINESS VALUE</span>
            <span class="mx-8 text-black font-medium">/ PURPOSEFUL DESIGNS</span>
        </div>
    </div>
</section>

<section id="who-we-are" class="py-32 px-6 bg-[#F3F4F6]">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12 flex items-center gap-3">
            <span class="w-2 h-2 bg-black rounded-full"></span>
            <h3 class="text-sm font-bold uppercase tracking-widest">Who we are</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="reveal bg-gray-200 rounded-[2rem] aspect-square md:aspect-video flex items-center justify-center relative group hover-target overflow-hidden">
                <span class="absolute bottom-6 left-6 font-medium z-10">Vektora Showreel</span>
                <div class="absolute inset-0 bg-black/5 group-hover:bg-black/10 transition-colors"></div>
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-500 cursor-pointer z-10">
                    <i data-feather="play" class="fill-black w-6 h-6 ml-1"></i>
                </div>
            </div>

            <div class="reveal">
                <h2 class="text-4xl md:text-6xl font-bold mb-8 leading-[1.1] tracking-tight">We are design-first creative studio</h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-10">
                    We believe in the power of purposeful design to solve real business challenges. Every line, color, and interaction is crafted with intent, creating experiences that connect and drive impact.
                </p>
                <a href="#" class="inline-flex items-center gap-2 px-8 py-4 bg-black text-white rounded-full font-bold btn-invert btn-black hover-target">
                    About us <i data-feather="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<section id="featured" class="py-32 px-6 bg-white rounded-t-[3rem]">
    <div class="max-w-7xl mx-auto">
        <div class="mb-24">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-2 h-2 bg-black rounded-full"></span>
                <h3 class="text-sm font-bold uppercase tracking-widest">Featured Works</h3>
            </div>
            <h2 class="text-5xl md:text-7xl font-bold reveal tracking-tight">We create solutions but most <br> importantly we identify problems.</h2>
        </div>

        <div class="reveal mb-32 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center group">
            <div class="lg:col-span-4">
                <div class="mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-black rounded-full"></span>
                    <span class="text-sm font-bold">Skincare Brand, US</span>
                </div>
                <h3 class="text-5xl font-bold mb-6 group-hover:underline decoration-2 underline-offset-8 transition-all">Scriptderm</h3>
                <p class="text-gray-500 mb-8 leading-relaxed text-lg">
                    Scriptderm is a dermatology-led skincare brand and direct-to-consumer platform.
                </p>
                <div class="flex gap-3">
                    <span class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Branding</span>
                    <span class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Web Design</span>
                </div>
            </div>
            <div class="lg:col-span-8">
                <div class="bg-[#F0F4FF] p-10 md:p-16 rounded-[2.5rem] hover-target transition-transform duration-700 hover:scale-[1.02]">
                    <img src="https://assets.website-files.com/63c53549646b533606f7b78f/63c552098670c27380108398_Scriptderm-p-800.png" alt="Project" class="w-full h-auto shadow-2xl rounded-xl">
                </div>
            </div>
        </div>

        <div class="reveal grid grid-cols-1 lg:grid-cols-12 gap-12 items-center group">
            <div class="lg:col-span-8 order-2 lg:order-1">
                <div class="bg-[#111] p-10 md:p-16 rounded-[2.5rem] hover-target transition-transform duration-700 hover:scale-[1.02]">
                    <img src="https://assets.website-files.com/63c53549646b533606f7b78f/63c5553e8670c223a4108865_Sophisticated%20Traders-p-800.png" alt="Project" class="w-full h-auto shadow-2xl rounded-xl opacity-90">
                </div>
            </div>
            <div class="lg:col-span-4 order-1 lg:order-2">
                <div class="mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 bg-black rounded-full"></span>
                    <span class="text-sm font-bold">Web3, FR</span>
                </div>
                <h3 class="text-5xl font-bold mb-6 group-hover:underline decoration-2 underline-offset-8 transition-all">Sophisticated Traders</h3>
                <p class="text-gray-500 mb-8 leading-relaxed text-lg">
                    A proprietary trading and fintech collective that merges data science.
                </p>
                <div class="flex gap-3">
                    <span class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Branding</span>
                    <span class="px-4 py-2 border border-gray-200 rounded-full text-xs font-bold uppercase tracking-wider">Web Design</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="services" class="py-32 px-6 bg-[#F3F4F6]">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12 flex items-center gap-3">
            <span class="w-2 h-2 bg-black rounded-full"></span>
            <h3 class="text-sm font-bold uppercase tracking-widest">Our services</h3>
        </div>
        
        <div class="bg-white rounded-[3rem] p-8 md:p-16 shadow-sm">
             <div class="space-y-4">
                @php $services = ['Brand Design', 'UI/UX Design', 'Motion Design', 'Graphic Design', 'Illustration']; @endphp
                @foreach($services as $index => $service)
                <div class="group flex items-center justify-between py-8 border-b border-gray-100 hover:px-6 transition-all duration-300 cursor-pointer hover-target">
                    <div class="flex items-center gap-8">
                        <span class="text-sm font-mono text-gray-300 w-8">0{{ $index + 1 }}</span>
                        <span class="text-2xl md:text-4xl font-medium group-hover:font-bold transition-all">{{ $service }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all group-hover:bg-black group-hover:border-black">
                        <i data-feather="arrow-right" class="w-5 h-5 text-white"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<section id="testimonials" class="py-32 px-6 bg-[#F3F4F6]">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12 flex items-center gap-3">
            <span class="w-2 h-2 bg-black rounded-full"></span>
            <h3 class="text-sm font-bold uppercase tracking-widest">Testimonials</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-12 rounded-[2rem] hover:shadow-xl transition-all duration-500 reveal">
                <div class="mb-8">
                     <div class="flex text-yellow-400 mb-4 gap-1">
                        @for($i=0;$i<5;$i++) <i data-feather="star" class="w-4 h-4 fill-current"></i> @endfor
                    </div>
                    <p class="text-xl text-gray-800 leading-relaxed font-medium">
                        "Vektora did a great job understanding our business. They came up with a design that is fresh and timeless. Can't wait for the world to see."
                    </p>
                </div>
                <div class="flex items-center justify-between mt-auto">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-black text-white rounded-full flex items-center justify-center font-bold text-sm">MV</div>
                        <div>
                            <h5 class="font-bold text-lg">Mav</h5>
                            <span class="text-sm text-gray-500">CEO, GenAI</span>
                        </div>
                    </div>
                    <a href="#" class="w-12 h-12 border rounded-full flex items-center justify-center hover:bg-black hover:text-white transition-colors hover-target"><i data-feather="linkedin" class="w-5 h-5"></i></a>
                </div>
            </div>
            
            <div class="bg-white p-12 rounded-[2rem] hover:shadow-xl transition-all duration-500 reveal">
                <div class="mb-8">
                    <div class="flex text-yellow-400 mb-4 gap-1">
                        @for($i=0;$i<5;$i++) <i data-feather="star" class="w-4 h-4 fill-current"></i> @endfor
                    </div>
                    <p class="text-xl text-gray-800 leading-relaxed font-medium">
                        "The new visual identity perfectly captures our vision. The team was responsive, professional, and quick in their communication."
                    </p>
                </div>
                <div class="flex items-center justify-between mt-auto">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-full overflow-hidden">
                             <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h5 class="font-bold text-lg">Mohammed Al Abri</h5>
                            <span class="text-sm text-gray-500">Investment Director</span>
                        </div>
                    </div>
                     <a href="#" class="w-12 h-12 border rounded-full flex items-center justify-center hover:bg-black hover:text-white transition-colors hover-target"><i data-feather="linkedin" class="w-5 h-5"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-32 px-6 bg-white rounded-t-[4rem]">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12 flex items-center gap-3">
            <span class="w-2 h-2 bg-black rounded-full"></span>
            <h3 class="text-sm font-bold uppercase tracking-widest">We're explorers</h3>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
            <div>
                <h2 class="text-6xl md:text-8xl font-bold mb-10 leading-none tracking-tighter">Ready to take <br> next step <br> with us?</h2>
                <div class="inline-flex px-8 py-4 border rounded-full text-gray-500 hover:text-black hover:border-black transition-colors hover-target cursor-pointer items-center gap-2">
                    Contact us <i data-feather="arrow-down-right" class="w-4 h-4"></i>
                </div>
            </div>

            <div class="bg-white">
                <h3 class="text-4xl font-bold mb-10">Let's make an impact</h3>
                <form class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="group relative">
                            <input type="text" placeholder=" " class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                            <label class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Name</label>
                        </div>
                        <div class="group relative">
                             <input type="text" placeholder=" " class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                            <label class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Company</label>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="group relative">
                             <input type="email" placeholder=" " class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                            <label class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Email</label>
                        </div>
                        <div class="group relative">
                             <input type="text" placeholder=" " class="peer w-full border-b border-gray-200 py-3 outline-none focus:border-black transition-colors bg-transparent pt-4">
                            <label class="absolute left-0 top-0 text-xs font-bold uppercase text-gray-400 transition-all peer-focus:text-black">Phone</label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase mb-6 text-gray-400">I'm interested in...</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['Branding', 'Website Design', 'UX/UI', 'Motion Design', 'Landing page', 'Content Creation'] as $tag)
                            <label class="cursor-pointer hover-target">
                                <input type="checkbox" class="peer sr-only">
                                <span class="px-6 py-3 border rounded-full text-sm block transition-all peer-checked:bg-black peer-checked:text-white peer-checked:border-black hover:border-black">
                                    {{ $tag }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button class="w-full py-5 bg-black text-white rounded-full font-bold text-lg btn-invert btn-black hover-target mt-8">
                        Submit Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection