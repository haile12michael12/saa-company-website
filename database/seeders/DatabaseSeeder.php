<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogSectionSetting;
use App\Models\Category;
use App\Models\ContactSectionSetting;
use App\Models\Experienace;
use App\Models\Feedback;
use App\Models\FeedbackSectionSetting;
use App\Models\FooterContactInfo;
use App\Models\FooterHelpLink;
use App\Models\FooterInfo;
use App\Models\FooterSocialLink;
use App\Models\FooterUsefulLink;
use App\Models\GeneralSetting;
use App\Models\Hero;
use App\Models\PortfolioItem;
use App\Models\PortfolioSectionSetting;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\SkillItem;
use App\Models\SkillSectionSetting;
use App\Models\TyperTitle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Company
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'SAA Digital Solutions',
            'slug' => 'saa-digital-solutions',
            'email' => 'contact@saacompany.com',
            'phone' => '+1 (555) 234-5678',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $company = DB::table('companies')->where('slug', 'saa-digital-solutions')->first();
        if ($company) {
            $companyId = $company->id;
        } else {
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'SAA Digital Solutions',
                'slug' => 'saa-digital-solutions',
                'email' => 'contact@saacompany.com',
                'phone' => '+1 (555) 234-5678',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@saacompany.com'],
            [
                'name' => 'SAA Admin',
                'password' => Hash::make('password123'),
                'company_id' => $companyId,
                'email_verified_at' => now(),
            ]
        );

        // General Setting
        GeneralSetting::updateOrCreate(
            ['id' => 1],
            [
                'logo' => 'frontend/assets/images/logo.png',
                'footer_logo' => 'frontend/assets/images/logo.png',
                'favicon' => 'frontend/assets/images/favicon.ico',
            ]
        );

        // SEO Setting
        SeoSetting::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'SAA Company - Modern Digital Solutions & Engineering',
                'description' => 'SAA delivers high-performance web applications, mobile apps, UI/UX design, and AI solutions.',
                'keywords' => 'web development, mobile app, UI/UX, AI assistant, software engineering, cloud services',
            ]
        );

        // Hero
        Hero::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Building Next-Generation Digital Experiences',
                'sub_title' => 'We design, engineer, and deploy high-performance web applications, mobile platforms, and AI-driven solutions that help businesses scale with confidence.',
                'btn_text' => 'Get a Free Quote',
                'btn_url' => url('/quote-request'),
                'image' => 'frontend/assets/images/hero-bg.jpg',
            ]
        );

        // Typer Titles
        TyperTitle::truncate();
        TyperTitle::create(['title' => 'Custom Web Engineering']);
        TyperTitle::create(['title' => 'Mobile App Innovation']);
        TyperTitle::create(['title' => 'UI/UX & Product Design']);
        TyperTitle::create(['title' => 'AI Automation & Cloud Solutions']);

        // Services
        Service::truncate();
        Service::create([
            'name' => 'Custom Web Application Development',
            'slug' => 'custom-web-application-development',
            'icon' => 'fal fa-laptop-code',
            'description' => 'Enterprise-grade, scalable web applications built using cutting-edge modern architectures and responsive UX.',
            'long_description' => 'We design and build bespoke web applications engineered for speed, high concurrency, and unmatched reliability. Whether developing complex SaaS platforms, client portals, or business management suites, our solutions leverage modern frameworks, cloud-native deployments, and strict security compliance.',
            'features' => json_encode([
                'Responsive full-stack development',
                'RESTful & GraphQL API integrations',
                'Role-based access control & enterprise security',
                'Real-time data synchronization & analytics',
                'Microservices & headless architectures',
            ]),
            'price' => 'From $2,500',
        ]);

        Service::create([
            'name' => 'Mobile App Development',
            'slug' => 'mobile-app-development',
            'icon' => 'fal fa-mobile-android',
            'description' => 'Native and cross-platform mobile apps for iOS and Android delivering fluid touch interactions and offline capability.',
            'long_description' => 'Our mobile engineering practice builds intuitive mobile applications that captivate users from first launch. From biometric authentication to push notifications, offline caching, and native hardware integration, we build apps that scale to millions of active downloads.',
            'features' => json_encode([
                'Cross-platform Flutter and React Native builds',
                'Native iOS and Android optimizations',
                'Seamless payment gateways & in-app purchases',
                'Offline storage & background sync',
                'App Store & Google Play publishing support',
            ]),
            'price' => 'From $3,000',
        ]);

        Service::create([
            'name' => 'UI/UX & Product Design',
            'slug' => 'ui-ux-product-design',
            'icon' => 'fal fa-palette',
            'description' => 'User-first interface design, interactive prototypes, and unified design systems tailored for high conversion.',
            'long_description' => 'We create intuitive, visually stunning digital experiences rooted in thorough user research and behavioral psychology. We transform complex workflows into effortless interfaces that boost retention, eliminate user friction, and elevate brand prestige.',
            'features' => json_encode([
                'User research & persona mapping',
                'Wireframing & interactive Figma prototypes',
                'Scalable design systems & component libraries',
                'Conversion rate optimization (CRO)',
                'Accessibility audits & compliance',
            ]),
            'price' => 'From $1,500',
        ]);

        Service::create([
            'name' => 'AI Integration & Automation',
            'slug' => 'ai-integration-automation',
            'icon' => 'fal fa-robot',
            'description' => 'Intelligent workflow automations, custom AI assistants, LLM integrations, and predictive business insights.',
            'long_description' => 'Supercharge your business operations by infusing artificial intelligence into existing workflows. We integrate Large Language Models, custom knowledge bases, automated document processing, and predictive scoring algorithms to automate repetitive tasks and provide 24/7 customer engagement.',
            'features' => json_encode([
                'Custom AI assistants & chatbot integrations',
                'Retrieval-Augmented Generation (RAG) pipelines',
                'Workflow automation & webhook triggers',
                'Predictive lead scoring & business analytics',
                'Data privacy & secure LLM gateways',
            ]),
            'price' => 'From $3,500',
        ]);

        Service::create([
            'name' => 'Cloud Infrastructure & DevOps',
            'slug' => 'cloud-infrastructure-devops',
            'icon' => 'fal fa-cloud-upload',
            'description' => 'Zero-downtime CI/CD deployment pipelines, containerized environments, and 24/7 cloud management.',
            'long_description' => 'Ensure maximum uptime and horizontal scalability with tailored cloud architecture. We design resilient container clusters, automate testing and continuous delivery pipelines, and establish proactive system monitoring that safeguards your application against outages.',
            'features' => json_encode([
                'AWS, Google Cloud, and DigitalOcean architectures',
                'Docker containerization & orchestration',
                'Automated GitHub Actions CI/CD pipelines',
                'SSL management & automated database backups',
                '24/7 uptime monitoring & alerting',
            ]),
            'price' => 'From $2,000',
        ]);

        Service::create([
            'name' => 'API & Systems Integration',
            'slug' => 'api-systems-integration',
            'icon' => 'fal fa-network-wired',
            'description' => 'Connect CRM platforms, billing gateways, ERP systems, and webhooks into an integrated digital ecosystem.',
            'long_description' => 'Eliminate data silos across your organization with custom API integration and middleware. We seamlessly connect billing gateways (Stripe, PayPal, Telebirr, Chapa), CRM systems, enterprise ERPs, and automated notification services.',
            'features' => json_encode([
                'Secure payment gateway integrations',
                'Two-way CRM and ERP data sync',
                'Webhook listeners & automated queues',
                'Custom RESTful API development',
                'Comprehensive API documentation & SDKs',
            ]),
            'price' => 'From $1,800',
        ]);

        // About
        About::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Engineering Digital Excellence for Forward-Thinking Enterprises',
                'description' => '<p>SAA Digital Solutions is a premier technology studio specializing in custom software development, modern UI/UX design, and AI-driven automation. We partner with startups, growing enterprises, and established institutions to build scalable digital infrastructure that drives measurable business outcomes.</p><p>With a battle-tested team of engineers, designers, and cloud architects, we deliver products that combine speed, aesthetic refinement, and architectural integrity.</p>',
                'image' => 'frontend/assets/images/about-image.jpg',
                'resume' => 'frontend/assets/downloads/company-profile.pdf',
            ]
        );

        // Experience
        Experienace::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Over 8 Years of Delivering Mission-Critical Software Solutions',
                'description' => 'From early-stage MVPs to distributed enterprise platforms processing millions in transactions, we take pride in delivering on time, on budget, and beyond expectations.',
                'phone' => '+1 (555) 234-5678',
                'email' => 'contact@saacompany.com',
                'image' => 'frontend/assets/images/experience-image.jpg',
            ]
        );

        // Skill Section Setting & Items
        SkillSectionSetting::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Core Capabilities & Technical Expertise',
                'sub_title' => 'We deploy modern frameworks and engineering disciplines to achieve maximum performance and resilience.',
            ]
        );

        SkillItem::truncate();
        SkillItem::create(['name' => 'Full-Stack Web Engineering', 'percent' => 96]);
        SkillItem::create(['name' => 'Mobile App Architecture', 'percent' => 92]);
        SkillItem::create(['name' => 'UI/UX & Interactive Design', 'percent' => 94]);
        SkillItem::create(['name' => 'AI Integrations & Automation', 'percent' => 88]);
        SkillItem::create(['name' => 'Cloud Infrastructure & DevOps', 'percent' => 90]);

        // Categories & Portfolio
        Category::truncate();
        $catWeb = Category::create(['name' => 'Web Applications', 'slug' => 'web-applications']);
        $catMobile = Category::create(['name' => 'Mobile Apps', 'slug' => 'mobile-apps']);
        $catDesign = Category::create(['name' => 'UI/UX Design', 'slug' => 'ui-ux-design']);
        $catAI = Category::create(['name' => 'AI & Cloud', 'slug' => 'ai-and-cloud']);

        PortfolioSectionSetting::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Featured Portfolio & Client Case Studies',
                'sub_title' => 'Take a look at some of our recent product launches, mobile platforms, and enterprise solutions.',
            ]
        );

        PortfolioItem::truncate();
        PortfolioItem::create([
            'category_id' => $catMobile->id,
            'title' => 'Fintech Mobile Banking Suite',
            'slug' => 'fintech-mobile-banking-suite',
            'image' => 'frontend/assets/images/portfolio-1.jpg',
            'client' => 'Apex Financial Group',
            'website' => 'https://example.com/apex-fintech',
            'description' => '<p>A comprehensive mobile banking application featuring biometric login, instant peer-to-peer money transfers, multi-currency wallets, and automated expense tracking. Built with high-security financial compliance standards and sub-second transaction validation.</p>',
        ]);

        PortfolioItem::create([
            'category_id' => $catWeb->id,
            'title' => 'Multi-Vendor E-Commerce Marketplace',
            'slug' => 'multi-vendor-ecommerce-marketplace',
            'image' => 'frontend/assets/images/portfolio-2.jpg',
            'client' => 'OmniStore Global',
            'website' => 'https://example.com/omnistore',
            'description' => '<p>An agile e-commerce platform with multi-vendor storefronts, headless checkout, automated inventory synchronisation across physical and digital channels, and AI-driven personalized product recommendations.</p>',
        ]);

        PortfolioItem::create([
            'category_id' => $catAI->id,
            'title' => 'SaaS Business Intelligence & Analytics Hub',
            'slug' => 'saas-business-intelligence-hub',
            'image' => 'frontend/assets/images/portfolio-3.jpg',
            'client' => 'MetricsCloud Corp',
            'website' => 'https://example.com/metricscloud',
            'description' => '<p>A distributed analytics engine capable of ingesting and aggregating millions of daily business events. Features interactive drag-and-drop dashboard builders, automated anomaly detection, and scheduled executive reports.</p>',
        ]);

        PortfolioItem::create([
            'category_id' => $catDesign->id,
            'title' => 'Telehealth Patient Portal & Booking Suite',
            'slug' => 'telehealth-patient-portal-booking',
            'image' => 'frontend/assets/images/portfolio-4.jpg',
            'client' => 'CarePulse Health Systems',
            'website' => 'https://example.com/carepulse',
            'description' => '<p>A HIPAA-compliant telemedicine platform designed with patient ease in mind. Includes secure video consultations, prescription management, automated reminders, and seamless EHR data synchronization.</p>',
        ]);

        // Feedback / Reviews
        FeedbackSectionSetting::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Client Testimonials & Reviews',
                'sub_title' => 'Real feedback from founders, CTOs, and product directors who rely on our software daily.',
            ]
        );

        Feedback::truncate();
        Feedback::create([
            'name' => 'Sarah Jenkins',
            'position' => 'Chief Technology Officer, Apex Financial',
            'description' => 'SAA Company delivered our financial application two weeks ahead of schedule. The code quality, security posture, and responsive communication were truly exceptional.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        Feedback::create([
            'name' => 'David Miller',
            'position' => 'Founder & CEO, OmniStore Global',
            'description' => 'Working with the team was an absolute pleasure. Their attention to UX detail and scalable cloud architecture helped us handle 10x traffic during Black Friday without a hitch.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        Feedback::create([
            'name' => 'Dr. Elena Rostova',
            'position' => 'Head of Product, CarePulse Health',
            'description' => 'The consultation and discovery process was clear and insightful. They understood our complex regulatory needs and built a seamless, intuitive patient portal.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        Feedback::create([
            'name' => 'Marcus Vance',
            'position' => 'Operations Director, LogiTrack Systems',
            'description' => 'Their AI assistant integration and workflow automation saved our customer support team over 30 hours each week. Highly recommended for any ambitious company!',
            'rating' => 5,
            'is_featured' => true,
        ]);

        // Blog
        BlogCategory::truncate();
        $blogCatEng = BlogCategory::create(['name' => 'Software Engineering', 'slug' => 'software-engineering']);
        $blogCatDesign = BlogCategory::create(['name' => 'UI/UX & Product', 'slug' => 'ui-ux-product']);
        $blogCatAI = BlogCategory::create(['name' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence']);

        BlogSectionSetting::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Latest Insights & Engineering Articles',
                'sub_title' => 'Thought leadership on modern software development, design systems, and digital strategy.',
            ]
        );

        Blog::truncate();
        Blog::create([
            'category' => $blogCatEng->id,
            'title' => 'Architecting Resilient Web Applications in 2026',
            'slug' => 'architecting-resilient-web-applications-in-2026',
            'image' => 'frontend/assets/images/blog-1.jpg',
            'description' => '<p>Modern web architecture requires a thoughtful balance between fast frontend rendering, resilient microservices, and secure API boundaries. In this deep dive, we explore how decoupled components, intelligent caching, and robust database indexing prevent bottlenecks as traffic scales exponentially.</p><p>By leveraging edge caching and event-driven architectures, development teams can achieve sub-100ms response times worldwide.</p>',
        ]);

        Blog::create([
            'category' => $blogCatDesign->id,
            'title' => 'Designing for High-Conversion: The 2026 UI/UX Playbook',
            'slug' => 'designing-for-high-conversion-ui-ux-playbook',
            'image' => 'frontend/assets/images/blog-2.jpg',
            'description' => '<p>Great product design goes far beyond aesthetics. It bridges cognitive ease with delightful user interactions. In this guide, we break down how micro-interactions, responsive form feedback, and accessible typography eliminate checkout abandonments and boost user retention.</p>',
        ]);

        Blog::create([
            'category' => $blogCatAI->id,
            'title' => 'How AI Assistants Are Transforming Customer Support in Small Businesses',
            'slug' => 'how-ai-assistants-transform-customer-support',
            'image' => 'frontend/assets/images/blog-3.jpg',
            'description' => '<p>From 24/7 inquiry triage to instant quotation recommendations, explore how growing businesses are deploying conversational AI without sacrificing the human touch. We examine real-world case studies demonstrating a 60% reduction in first-response times.</p>',
        ]);

        // Contact Section Setting
        ContactSectionSetting::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Get in Touch with Our Team',
                'sub_title' => 'Have a project in mind or want to explore how our digital solutions can help your business? Send us a message below.',
            ]
        );

        // Footer Social
        FooterSocialLink::truncate();
        FooterSocialLink::create(['icon' => 'fab fa-twitter', 'url' => 'https://twitter.com']);
        FooterSocialLink::create(['icon' => 'fab fa-github', 'url' => 'https://github.com']);
        FooterSocialLink::create(['icon' => 'fab fa-linkedin', 'url' => 'https://linkedin.com']);
        FooterSocialLink::create(['icon' => 'fab fa-dribbble', 'url' => 'https://dribbble.com']);

        // Footer Info
        FooterInfo::updateOrCreate(
            ['id' => 1],
            [
                'info' => 'We craft modern digital products, enterprise web platforms, and intelligent automated systems that empower ambitious businesses to scale.',
                'copy_right' => '© 2026 SAA Digital Solutions. All Rights Reserved.',
                'powered_by' => 'Engineered with Precision & Passion',
            ]
        );

        // Footer Contact
        FooterContactInfo::updateOrCreate(
            ['id' => 1],
            [
                'address' => '123 Innovation Way, Tech District, Suite 500',
                'phone' => '+1 (555) 234-5678',
                'email' => 'contact@saacompany.com',
            ]
        );

        // Footer Useful Links
        FooterUsefulLink::truncate();
        FooterUsefulLink::create(['name' => 'About Company', 'url' => url('/about')]);
        FooterUsefulLink::create(['name' => 'Our Services', 'url' => url('/services')]);
        FooterUsefulLink::create(['name' => 'Portfolio Projects', 'url' => url('/portfolio')]);
        FooterUsefulLink::create(['name' => 'Engineering Blog', 'url' => url('/blog')]);
        FooterUsefulLink::create(['name' => 'Request a Quote', 'url' => url('/quote-request')]);
        FooterUsefulLink::create(['name' => 'Book Consultation', 'url' => url('/book-consultation')]);

        // Footer Help Links
        FooterHelpLink::truncate();
        FooterHelpLink::create(['name' => 'Frequently Asked Questions', 'url' => url('/faq')]);
        FooterHelpLink::create(['name' => 'Client Reviews & Ratings', 'url' => url('/reviews')]);
        FooterHelpLink::create(['name' => 'Public AI Assistant', 'url' => url('/ai-assistant')]);
        FooterHelpLink::create(['name' => 'Contact Support', 'url' => url('/contact')]);

        // FAQs
        DB::table('faqs')->truncate();
        DB::table('faqs')->insert([
            [
                'question' => 'What digital services does SAA offer?',
                'answer' => 'We provide complete full-cycle software solutions including custom web application development, mobile apps for iOS and Android, UI/UX product design, AI assistant integrations, cloud infrastructure/DevOps, and enterprise API integrations.',
                'category' => 'Services',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How does the quotation request process work?',
                'answer' => 'You can submit your project requirements through our Quote Request form on /quote-request. Select the services needed, specify your budget range and target timeline, and provide project notes. Our technical team reviews the brief and returns a detailed estimate within 24–48 hours.',
                'category' => 'Pricing & Quotes',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I schedule a consultation before starting a project?',
                'answer' => 'Yes! We encourage free 30-minute discovery consultations via Google Meet, Zoom, or phone call. You can select your preferred date, time slot, and discussion topics directly through our Book Consultation page (/book-consultation).',
                'category' => 'Consultations',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'What is the typical timeline for an MVP or web application?',
                'answer' => 'Standard MVP web applications typically take 4 to 8 weeks to design, develop, and launch. Enterprise platforms and multi-sided marketplaces generally span 8 to 16 weeks, delivered through agile two-week development sprints with live review demos.',
                'category' => 'Services',
                'order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'How does the Public AI Assistant work?',
                'answer' => 'Our Public AI Assistant on /ai-assistant is an interactive intelligent guide trained on our company information, technical capabilities, case studies, and pricing models. You can ask questions in natural language, request recommendations, and receive direct links to book appointments or request quotations.',
                'category' => 'General',
                'order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Do you provide post-launch maintenance, hosting, and SLA support?',
                'answer' => 'Yes. We offer comprehensive ongoing support packages covering server monitoring, security patching, automated backups, performance optimization, and continuous feature enhancements.',
                'category' => 'Support',
                'order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'question' => 'Can I submit a review or client testimonial?',
                'answer' => 'Absolutely! Visitors and clients can read feedback and submit their own experiences directly on our Reviews page (/reviews). We value transparent feedback from every partner we collaborate with.',
                'category' => 'General',
                'order' => 7,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
