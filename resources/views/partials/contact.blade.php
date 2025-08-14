@php
    $contact = \App\Models\Contact::getActiveContact();
@endphp

<!-- Contact Section -->
<section id="contact" class="bg-gray-100 py-16">
    <!-- Section Title -->
    <div class="container mx-auto text-center mb-12" data-aos="fade-up">
        <h2 class="text-3xl font-bold">Hubungi Kami</h2>
    </div>

    <div class="container mx-auto px-4" data-aos="fade-up" data-aos-delay="100">
        <div class="flex flex-wrap -mx-4 gap-y-8">
            <!-- Contact Information -->
            <div class="w-full lg:w-5/12 px-4">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    @if($contact)
                        <!-- Address -->
                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="200">
                            <i class="fas fa-map-marker-alt text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Alamat</h3>
                                <p class="text-gray-600">{{ $contact->address }}</p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="300">
                            <i class="fas fa-phone-alt text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Telepon</h3>
                                <p class="text-gray-600">{{ $contact->phone }}</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="400">
                            <i class="fas fa-envelope text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Email</h3>
                                <p class="text-gray-600">{{ $contact->email }}</p>
                            </div>
                        </div>

                        @if($contact->website)
                        <!-- Website -->
                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="500">
                            <i class="fas fa-globe text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Website</h3>
                                <a href="{{ $contact->website }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ $contact->website }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($contact->opening_hours)
                        <!-- Opening Hours -->
                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="600">
                            <i class="fas fa-clock text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Jam Operasional</h3>
                                <div class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $contact->opening_hours }}</div>
                            </div>
                        </div>
                        @endif

                        <!-- Map -->
                        @if($contact->map_embed)
                        <div class="mt-6" data-aos="fade-up" data-aos-delay="700">
                            {!! $contact->map_embed !!}
                        </div>
                        @endif
                    
                    @else
                        <!-- Default Contact Info -->
                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="200">
                            <i class="fas fa-map-marker-alt text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Alamat</h3>
                                <p class="text-gray-600">Komplek BNI 46, pesing, Wijaya Kusuma, West Jakarta City</p>
                            </div>
                        </div>

                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="300">
                            <i class="fas fa-phone-alt text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Telepon</h3>
                                <p class="text-gray-600">(021) 5660066</p>
                            </div>
                        </div>

                        <div class="flex items-start mb-6" data-aos="fade-up" data-aos-delay="400">
                            <i class="fas fa-envelope text-2xl text-blue-600 mr-4"></i>
                            <div>
                                <h3 class="text-xl font-bold">Email</h3>
                                <p class="text-gray-600">Smatunasharapanadm@gmail.com</p>
                            </div>
                        </div>

                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.817702041258!2d106.76924517592815!3d-6.155164760331785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f7005875f537%3A0x1572a91c0e6bf97d!2sSMA%20Tunas%20Harapan!5e0!3m2!1sid!2sid!4v1753807208956!5m2!1sid!2sid" width="100%" height="290" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    @endif
                </div>
            </div>

            <!-- Contact Form -->
            <div class="w-full lg:w-7/12 px-4">
                <form action="{{ route('contact.store') }}" method="post" class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="200">
                    @csrf
                    
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex flex-wrap -mx-3">

                        <div class="w-full md:w-1/2 px-3 mb-6">
                            <label for="name-field" class="block mb-2">Your Name</label>
                            <input type="text" name="name" id="name-field" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('name') border-red-500 @enderror" value="{{ old('name') }}" required="">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full md:w-1/2 px-3 mb-6">
                            <label for="email-field" class="block mb-2">Your Email</label>
                            <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('email') border-red-500 @enderror" name="email" id="email-field" value="{{ old('email') }}" required="">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full px-3 mb-6">
                            <label for="subject-field" class="block mb-2">Subject</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md @error('subject') border-red-500 @enderror" name="subject" id="subject-field" value="{{ old('subject') }}" required="">
                            @error('subject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full px-3 mb-6">
                            <label for="message-field" class="block mb-2">Message</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md @error('message') border-red-500 @enderror" name="message" rows="5" id="message-field" required="">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full px-3 text-center">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">Send Message</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
