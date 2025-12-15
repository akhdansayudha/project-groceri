<nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 transition-all">
    <div class="max-w-7xl mx-auto px-6 py-5 flex justify-between items-center relative">
        
        <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tighter hover-target z-50 relative">
            vektora<span class="text-blue-600">.</span>
        </a>

        <div class="hidden md:flex space-x-16 items-center">
            <a href="#" class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Home</a>
            <a href="#who-we-are" class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">About</a>
            
            <div class="group static"> 
                <button class="text-sm font-medium hover:text-gray-500 transition-colors flex items-center gap-1 hover-target py-2">
                    Services <i data-feather="chevron-down" class="w-4 h-4"></i>
                </button>
                
                <div class="mega-menu invisible opacity-0 absolute top-[80px] left-1/2 w-[90vw] max-w-4xl bg-white rounded-3xl p-10 shadow-2xl border border-gray-100 z-40">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                        <div>
                            <h4 class="text-gray-400 text-xs uppercase tracking-widest mb-6 font-bold">Design</h4>
                            <ul class="space-y-4">
                                <li><a href="#" class="text-xl font-medium hover:text-blue-600 hover-target block">Branding</a></li>
                                <li><a href="#" class="text-xl font-medium hover:text-blue-600 hover-target block">Website Design</a></li>
                                <li><a href="#" class="text-xl font-medium hover:text-blue-600 hover-target block">UI/UX</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-gray-400 text-xs uppercase tracking-widest mb-6 font-bold">Development</h4>
                            <ul class="space-y-4">
                                <li><a href="#" class="text-xl font-medium hover:text-blue-600 hover-target block">Webflow Dev</a></li>
                                <li><a href="#" class="text-xl font-medium hover:text-blue-600 hover-target block">Shopify</a></li>
                                <li><a href="#" class="text-xl font-medium hover:text-blue-600 hover-target block">React / Vue</a></li>
                            </ul>
                        </div>
                         <div class="hidden md:block bg-gray-100 rounded-xl h-full min-h-[150px]"></div>
                    </div>
                </div>
            </div>

            <a href="#featured" class="text-sm font-medium hover:text-gray-500 transition-colors hover-target">Projects</a>
        </div>

        <a href="{{ route('login') }}" class="hidden md:inline-block px-6 py-2.5 rounded-full text-sm font-bold btn-invert btn-black hover-target">
            Client Login
        </a>

        <div class="md:hidden hover-target">
            <i data-feather="menu"></i>
        </div>
    </div>
</nav>