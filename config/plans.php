<?php

return [
    'currency' => 'ETB',
    'plans' => [
        'free' => [
            'name' => 'Free',
            'code' => 'free',
            'price' => 0,
            'interval' => 'forever',
            'description' => 'Essential reading and personal library management.',
            'features' => [
                'Discover & search book catalog',
                'Personal digital library (Want to Read, Reading, Completed)',
                'Reading progress tracking & daily page goal',
                'Consecutive day streak counter',
                'Save favorite quotes & write reviews',
                'Access to authorized open-licensed PDFs',
                'Basic category-based recommendations',
            ],
            'limitations' => [
                'No advanced velocity analytics',
                'No personalized reading schedules',
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'code' => 'premium',
            'price' => 99.00,
            'interval' => 'month',
            'description' => 'For ambitious readers who want in-depth analytics and targeted reading plans.',
            'badge' => 'Most Popular',
            'features' => [
                'Everything included in the Free plan',
                'Advanced Reading Velocity & Completion Forecasts',
                'Deep Reading Habit Analytics (Daily/Weekly/Monthly)',
                'Personalized Custom Reading Schedules & Target Milestones',
                'Dynamic Page-Pacing Calculations',
                'Exclusive Member Badge across discussions',
                'Priority support & early access to catalog drops',
            ],
            'limitations' => [],
        ],
    ],

    'upcoming_features' => [
        [
            'title' => 'AI Reading Companion & Chapter Q&A',
            'badge' => 'In Active Development',
            'badge_color' => 'amber',
            'description' => 'Ask in-depth questions about complex philosophical arguments in Plato or Nietzsche, receive instant chapter breakdowns, and test your comprehension.',
            'icon' => 'sparkles',
        ],
        [
            'title' => 'Audiobook Voice Narration & Audio Sync',
            'badge' => 'Coming Soon',
            'badge_color' => 'blue',
            'description' => 'Listen to voice narrations of your favorite classic public domain books with real-time text synchronization as you read.',
            'icon' => 'headphones',
        ],
        [
            'title' => 'Custom E-Reader Themes & Night Modes',
            'badge' => 'Coming Soon',
            'badge_color' => 'emerald',
            'description' => 'Warm Sepia, True OLED Black, custom serif typography, adjustable line height, and distraction-free full-screen reading modes.',
            'icon' => 'book-open',
        ],
        [
            'title' => 'Notion, Obsidian & Markdown Notes Export',
            'badge' => 'In Design',
            'badge_color' => 'purple',
            'description' => 'Export all your saved quotes, reflections, reading streak logs, and reviews straight into your personal knowledge base in one click.',
            'icon' => 'arrow-down-tray',
        ],
        [
            'title' => 'Private Reading Clubs & Friend Milestones',
            'badge' => 'Planned',
            'badge_color' => 'stone',
            'description' => 'Form reading groups with colleagues or friends, track shared book completion targets, and exchange insights.',
            'icon' => 'user-group',
        ],
        [
            'title' => 'Smart Calendar Sync & Streak Protection Nudges',
            'badge' => 'Planned',
            'badge_color' => 'stone',
            'description' => 'Automatically integrate your personalized daily reading pace into Google or Apple Calendar with gentle reminders to protect your streak.',
            'icon' => 'calendar',
        ],
    ],
];
