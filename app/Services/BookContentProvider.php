<?php

namespace App\Services;

class BookContentProvider
{
    /**
     * Get chapters and structured text for a given book slug.
     *
     * @return array{chapters: array<string>, content: array<int, array{title: string, subtitle: string, paragraphs: array<string>, takeaway: string}>}
     */
    public static function getBookData(string $slug): array
    {
        $books = [
            'meditations' => [
                'chapters' => [
                    'Debts and Lessons in Virtue',
                    'On the River Gran (Morning Reflections)',
                    'The Nature of the Soul and Fate',
                    'Internal Tranquility and the Citadel',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: Debts and Lessons in Virtue',
                        'subtitle' => 'From Book I: What I Owe to My Forebears and Mentors',
                        'paragraphs' => [
                            'From my grandfather Verus I learned good morals and the government of my temper. From the reputation and remembrance of my father, modesty and a manly character. From my mother, piety and beneficence, and abstinence, not only from evil deeds, but even from evil thoughts; and further, simplicity in my way of living, far removed from the habits of the rich.',
                            'From my great-grandfather, not to have frequented public schools, and to have had good teachers at home, and to have learned that on such things a man should spend liberally.',
                            'From Rusticus I received the impression that my character required improvement and discipline; and from him I learned not to be led astray to sophistic emulation, nor to writing on speculative matters, nor to delivering little moralising lectures, nor to showing myself off as a man who does great ascetic exercises, or does noble acts to make an impression.',
                        ],
                        'takeaway' => 'True virtue begins with gratitude and recognizing that our moral character is forged by the examples we choose to emulate.',
                    ],
                    [
                        'title' => 'Chapter 2: Morning Reflections',
                        'subtitle' => 'From Book II: Meeting the Trials of Human Society',
                        'paragraphs' => [
                            'When you wake up in the morning, tell yourself: The people I deal with today will be meddling, ungrateful, arrogant, dishonest, jealous, and surly. They are like this because they cannot distinguish good from evil.',
                            'But I have seen the beauty of good, and the ugliness of evil, and have recognized that the wrongdoer has a nature related to my own—not of the same blood or birth, but the same mind, and possessing a share of the divine.',
                            'And so none of them can hurt me. No one can implicate me in ugliness. Nor can I feel angry at my kin, or hate them. We were born to work together like feet, hands, and eyes, like the two rows of teeth, upper and lower. To obstruct each other is contrary to nature.',
                        ],
                        'takeaway' => 'You cannot control the rudeness of others, but you have absolute sovereignty over your own judgment and response.',
                    ],
                    [
                        'title' => 'Chapter 3: The Nature of the Soul',
                        'subtitle' => 'From Book IV: The Inner Citadel',
                        'paragraphs' => [
                            'People look for retreats for themselves, in the country, by the coast, or in the hills. There is nowhere that a person can find a more peaceful and trouble-free retreat than in his own mind. So constantly give yourself this retreat, and renew yourself.',
                            'Keep your brief and fundamental principles ready to hand, which will at once eliminate all sorrow and send you back without any irritation to the life to which you must return.',
                            'Remember: The soul dyes itself with the color of its thoughts. Things themselves have no hold on the soul; they stand outside, inert, and our troubles come only from the judgment within.',
                        ],
                        'takeaway' => 'Retreat inward. Circumstances have no power to disturb you; only your opinions about them create distress.',
                    ],
                    [
                        'title' => 'Chapter 4: The Ephemeral Nature of Time',
                        'subtitle' => 'From Book VII: Living in the Present Moment',
                        'paragraphs' => [
                            'Do not act as if you were going to live ten thousand years. Death hangs over you. While you live, while it is in your power, be good.',
                            'Time is a river, a violent current of events, glimpsed once and already carried past us, and another follows and is gone.',
                            'Cast away the opinion that you are harmed, and the harm itself is cast away. Stand like the promontory against which the waves continually break, yet it stands firm and tames the fury of the water around it.',
                        ],
                        'takeaway' => 'Act with integrity today. The past is unalterable, the future is uncertain, and the present is the only domain in which you exist.',
                    ],
                ],
            ],

            'atomic-habits' => [
                'chapters' => [
                    'The Fundamentals: The Power of 1% Compounding',
                    'The 1st Law: Make It Obvious',
                    'The 2nd Law: Make It Attractive',
                    'The 3rd Law: Make It Easy',
                    'The 4th Law: Make It Satisfying',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: The Surprising Power of Atomic Habits',
                        'subtitle' => 'Why Tiny Changes Make a Remarkable Difference',
                        'paragraphs' => [
                            'It is so easy to overestimate the importance of one defining moment and underestimate the value of making small improvements on a daily basis. Too often, we convince ourselves that massive success requires massive action.',
                            'Meanwhile, improving by 1 percent isn\'t particularly notable—sometimes it isn\'t even noticeable—but it can be far more meaningful, especially in the long run. If you can get 1 percent better each day for one year, you’ll end up thirty-seven times better by the time you’re done.',
                            'Habits are the compound interest of self-improvement. The same way that money multiplies through compound interest, the effects of your habits multiply as you repeat them. They seem to make little difference on any given day and yet the impact they deliver over months and years is enormous.',
                        ],
                        'takeaway' => 'You do not rise to the level of your goals. You fall to the level of your systems.',
                    ],
                    [
                        'title' => 'Chapter 2: The 1st Law — Make It Obvious',
                        'subtitle' => 'Environment Design and Implementation Intentions',
                        'paragraphs' => [
                            'Most people think they lack motivation when what they really lack is clarity. It is not always obvious when and where to take action. The implementation intention strategy is simple: I will [BEHAVIOR] at [TIME] in [LOCATION].',
                            'Furthermore, habit stacking allows you to pair a new habit with a current habit: After [CURRENT HABIT], I will [NEW HABIT]. If you want to read 20 pages every day, stack it: After I brew my morning cup of coffee, I will open my book and read for twenty minutes.',
                            'Environment is the invisible hand that shapes human behavior. Make the cues of good habits prominent and obvious in your surroundings, and invert the law for bad habits: make them invisible.',
                        ],
                        'takeaway' => 'Design an environment where doing the right thing is the path of least resistance.',
                    ],
                    [
                        'title' => 'Chapter 3: The 3rd Law — Make It Easy',
                        'subtitle' => 'The Law of Least Effort and the Two-Minute Rule',
                        'paragraphs' => [
                            'Human behavior follows the Law of Least Effort. We naturally gravitate toward the option that requires the least amount of work. When starting a new habit, it should take less than two minutes to do.',
                            'The point is not to do one thing. The point is to master the habit of showing up. A habit must be established before it can be improved. If you can\'t learn the basic skill of showing up, then you have little hope of mastering the finer details.',
                            'Standardize before you optimize. You can\'t improve a habit that doesn\'t exist. Read one page before you attempt to finish an entire chapter in a single sitting.',
                        ],
                        'takeaway' => 'Scale down your reading habit until it can be executed in two minutes. Show up every day.',
                    ],
                    [
                        'title' => 'Chapter 4: The 4th Law — Make It Satisfying',
                        'subtitle' => 'The Cardinal Rule of Behavior Change and Streak Tracking',
                        'paragraphs' => [
                            'What is immediately rewarded is repeated. What is immediately punished is avoided. The first three laws increase the odds that a behavior will be performed this time. The fourth law increases the odds that a behavior will be repeated next time.',
                            'Habit trackers and visual cues—like moving paperclips from one jar to another or maintaining an unbroken reading streak—provide immediate evidence of your progress. They satisfy your brain’s desire for immediate feedback.',
                            'Don’t break the chain. If you miss one day, get back on track immediately. Missing once is an accident. Missing twice is the start of a new, destructive habit.',
                        ],
                        'takeaway' => 'Never miss twice. When streaks break, restart on day one with relentless determination.',
                    ],
                ],
            ],

            'deep-work' => [
                'chapters' => [
                    'The Deep Work Hypothesis',
                    'Rule 1: Work Deeply',
                    'Rule 2: Embrace Boredom',
                    'Rule 3: Quit Social Media',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: The Deep Work Hypothesis',
                        'subtitle' => 'Why Focused Cognitive Effort Is a Modern Superpower',
                        'paragraphs' => [
                            'Deep Work: Professional activities performed in a state of distraction-free concentration that push your cognitive capabilities to their limit. These efforts create new value, improve your skill, and are hard to replicate.',
                            'Shallow Work: Non-cognitively demanding, logistical-style tasks, often performed while distracted. These efforts tend not to create much new value in the world and are easy to replicate.',
                            'The Deep Work Hypothesis: The ability to perform deep work is becoming increasingly rare at exactly the same time it is becoming increasingly valuable in our economy. As a consequence, the few who cultivate this skill, and then make it the core of their working life, will thrive.',
                        ],
                        'takeaway' => 'To thrive in the new economy, you must master the ability to quickly master hard things and produce at an elite level.',
                    ],
                    [
                        'title' => 'Chapter 2: Rule 1 — Work Deeply',
                        'subtitle' => 'Routines, Rituals, and Eliminating Attention Residue',
                        'paragraphs' => [
                            'You need to be systematic about your deep sessions. Willpower is a finite resource that depletes as you use it. To succeed with deep work, you must add rituals and routines to your working life designed to minimize the amount of your limited willpower necessary to transition into and maintain concentration.',
                            'When you switch from Task A to Task B, your attention does not immediately follow. A residue of your attention remains stuck thinking about the previous task. Even quick checks of email or notifications introduce debilitating attention residue.',
                            'Schedule every minute of your day into dedicated focus blocks. Guard uninterrupted reading and thinking time like a fortress.',
                        ],
                        'takeaway' => 'Ritualize your reading and focus sessions to bypass the friction of starting.',
                    ],
                    [
                        'title' => 'Chapter 3: Rule 2 — Embrace Boredom',
                        'subtitle' => 'Rewiring the Brain to Resist Distraction',
                        'paragraphs' => [
                            'The ability to concentrate intensely is a skill that must be trained. Much as athletes must take care of their bodies outside of training sessions, you’ll struggle to achieve the deepest levels of concentration if you spend the rest of your time fleeing boredom at the slightest provocation.',
                            'If every moment of potential boredom in your life—say, waiting in line or sitting at a traffic light—is relieved with a quick glance at your smartphone, then your brain has been rewired to be incapable of enduring the lack of stimuli necessary for deep work.',
                            'Don’t take breaks from distraction. Instead, take breaks from focus. Schedule in advance when you will allow yourself to use the internet, and keep the rest of the time clean.',
                        ],
                        'takeaway' => 'Train your mind to tolerate silence, boredom, and uninterrupted reading without digital escape.',
                    ],
                ],
            ],

            'the-psychology-of-money' => [
                'chapters' => [
                    'No One\'s Crazy: Personal Financial Lenses',
                    'Luck & Risk: Twin Sibling Forces',
                    'Confounding Compounding: The Warren Buffett Secret',
                    'Freedom: The Highest Form of Wealth',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: No One\'s Crazy',
                        'subtitle' => 'Your Personal Experiences Shape Your Financial Worldview',
                        'paragraphs' => [
                            'Your personal experiences with money make up maybe 0.00000001% of what\'s happened in the world, but maybe 80% of how you think the world works. Everyone has had a different upbringing, lived through different inflation cycles, different stock booms, and different recessions.',
                            'No one makes financial decisions in a vacuum. A person who grew up in poverty thinks about risk and reward in ways that the child of a wealthy tech executive cannot fathom. What seems crazy to you might make complete sense to someone else.',
                            'Doing well with money has a little to do with how smart you are and a lot to do with how you behave. Genius without behavioral control can be a disaster; the opposite is also true.',
                        ],
                        'takeaway' => 'Financial success is not a hard science; it is a soft skill where behavior trumps raw intelligence.',
                    ],
                    [
                        'title' => 'Chapter 2: Confounding Compounding',
                        'subtitle' => 'The Counterintuitive Geometry of Long-Term Growth',
                        'paragraphs' => [
                            'More than 99% of Warren Buffett\'s wealth was accumulated after his 50th birthday. If he had retired at 60 like normal workers, nobody would have ever heard his name.',
                            'His skill is investing, but his secret is time. That is how compounding works. Our minds are not built to intuitively grasp exponential growth; we think linearly.',
                            'Good investing isn\'t necessarily about earning the highest returns, because the highest returns tend to be one-off hits that can\'t be repeated. It\'s about earning pretty good returns that you can stick with and which can be repeated for the longest period of time.',
                        ],
                        'takeaway' => 'Patience and consistency over decades beat flashy, short-lived spikes every single time.',
                    ],
                    [
                        'title' => 'Chapter 3: Freedom',
                        'subtitle' => 'Controlling Your Time Is the Highest Dividend Money Pays',
                        'paragraphs' => [
                            'The highest form of wealth is the ability to wake up every morning and say, "I can do whatever I want today." People want to become wealthy to become happier. Happiness is a complicated subject, but if there\'s a common denominator in happiness—a universal fuel of joy—it\'s that people want to be in control of their lives.',
                            'The ability to do what you want, when you want, with who you want, for as long as you want, is priceless. It is the highest dividend money pays.',
                            'Using your money to buy time and options has a lifestyle benefit that few luxury goods can ever compete with.',
                        ],
                        'takeaway' => 'True wealth is autonomy. Use your discipline to purchase your independence.',
                    ],
                ],
            ],

            'the-art-of-war' => [
                'chapters' => [
                    'Laying Plans and Strategic Calculations',
                    'Waging War and Resource Discipline',
                    'Attack by Stratagem: The Supreme Art',
                    'Tactical Dispositions and Formations',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: Laying Plans',
                        'subtitle' => 'The Five Invariable Factors of War',
                        'paragraphs' => [
                            'Sun Tzu said: The art of war is of vital importance to the State. It is a matter of life and death, a road either to safety or to ruin. Hence it is a subject of inquiry which can on no account be neglected.',
                            'The art of war, then, is governed by five constant factors, to be taken into account in one\'s deliberations: The Moral Law; Heaven; Earth; The Commander; and Method and Discipline.',
                            'All warfare is based on deception. Hence, when able to attack, we must seem unable; when using our forces, we must seem inactive; when we are near, we must make the enemy believe we are far away; when far away, we must make him believe we are near.',
                        ],
                        'takeaway' => 'Victory belongs to those who calculate extensively before battle begins.',
                    ],
                    [
                        'title' => 'Chapter 2: Attack by Stratagem',
                        'subtitle' => 'Subduing the Enemy Without Fighting',
                        'paragraphs' => [
                            'In the practical art of war, the best thing of all is to take the enemy\'s country whole and intact; to shatter and destroy it is not so good. So, too, it is better to recapture an army entire than to destroy it.',
                            'Hence to fight and conquer in all your battles is not supreme excellence; supreme excellence consists in breaking the enemy\'s resistance without fighting.',
                            'If you know the enemy and know yourself, you need not fear the result of a hundred battles. If you know yourself but not the enemy, for every victory gained you will also suffer a defeat. If you know neither the enemy nor yourself, you will succumb in every battle.',
                        ],
                        'takeaway' => 'Self-knowledge and deep awareness of your competition eliminate the hazards of conflict.',
                    ],
                ],
            ],

            'as-a-man-thinketh' => [
                'chapters' => [
                    'Thought and Character',
                    'Effect of Thought on Circumstances',
                    'Effect of Thought on Health and the Body',
                    'Thought and Purpose: The Vision',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: Thought and Character',
                        'subtitle' => 'The Mind as the Master Weaver',
                        'paragraphs' => [
                            'The aphorism, "As a man thinketh in his heart so is he," not only embraces the whole of a man\'s being, but is so comprehensive as to reach out to every condition and circumstance of his life. A man is literally what he thinks, his character being the complete sum of all his thoughts.',
                            'As the plant springs from, and could not be without, the seed, so every act of a man springs from the hidden seeds of thought, and could not have appeared without them. This applies equally to those acts called "spontaneous" and "unpremeditated" as to those which are deliberately executed.',
                            'Act is the blossom of thought, and joy and suffering are its fruits; thus does a man garner in the sweet and bitter fruitage of his own husbandry.',
                        ],
                        'takeaway' => 'You do not attract what you want; you attract what you are in your habitual thought.',
                    ],
                    [
                        'title' => 'Chapter 2: Thought and Purpose',
                        'subtitle' => 'Connecting Thought to Definite Aims',
                        'paragraphs' => [
                            'Until thought is linked with purpose there is no intelligent accomplishment. With the majority the bark of thought is allowed to "drift" upon the ocean of life. Aimlessness is a vice, and such drifting must not continue for him who would steer clear of catastrophe.',
                            'They who have no central purpose in their life fall an easy prey to petty worries, fears, troubles, and self-pityings, all of which are indications of weakness, which lead, just as surely as deliberately planned sins, to failure and unhappiness.',
                            'A man should conceive of a legitimate purpose in his heart, and set about to achieve it. He should make this purpose the centralizing point of his thoughts.',
                        ],
                        'takeaway' => 'Fix your thoughts upon a definite, worthy ideal. Purpose shields the soul from drifting.',
                    ],
                ],
            ],

            'the-richest-man-in-babylon' => [
                'chapters' => [
                    'The Man Who Desired Gold',
                    'The Richest Man in Babylon',
                    'Seven Cures for a Lean Purse',
                    'The Five Laws of Gold',
                ],
                'content' => [
                    [
                        'title' => 'Chapter 1: The Man Who Desired Gold',
                        'subtitle' => 'Bansir the Chariot Builder and Kobbi the Musician',
                        'paragraphs' => [
                            'In ancient Babylon, Bansir sat upon the low wall surrounding his property, gazing mournfully upon his unfinished chariot. His wife frequently peered out, reminding him that the meal bag was empty and that he should be working.',
                            'Yet Bansir was deep in thought. "Why," he asked his friend Kobbi, "should we who have laboured all our lives under the desert sun remain forever poor, while our childhood companion Arkad is the wealthiest man in the empire?"',
                            'They realized that wealth was not an accident of birth or a gift of the gods; Arkad had sought out the laws of gold and applied them systematically. They decided to visit Arkad and learn the secrets of financial prosperity.',
                        ],
                        'takeaway' => 'Desiring wealth is not enough; one must seek counsel from those who have mastered its laws.',
                    ],
                    [
                        'title' => 'Chapter 2: Seven Cures for a Lean Purse',
                        'subtitle' => 'Arkad\'s Seven Timeless Rules',
                        'paragraphs' => [
                            'Before the assembled citizens in the Temple of Learning, Arkad stood forth and shared his seven cures for an empty purse:',
                            '1. Start thy purse to fattening: For every ten coins thou placest within thy purse, take out for use but nine. Thy purse will start at once to fatten.',
                            '2. Control thy expenditures: Confuse not necessary expenses with thy desires. Budget thy expenses that thou mayest have coins to pay for thy necessities and thy enjoyment without spending thy tenth part.',
                            '3. Make thy gold multiply: A man’s wealth is not in the coins he carries in his purse; it is in the income he buildeth, the golden stream that continually floweth into his purse.',
                            '4. Guard thy treasures from loss: Invest only where thy principal is safe, where it may be reclaimed if desirable, and where thou will not fail to collect a fair rental.',
                            '5. Make of thy dwelling a profitable investment. 6. Insure a future income for thy family. 7. Increase thy capacity to earn: cultivate thy powers, study, and respect thyself.',
                        ],
                        'takeaway' => 'A part of all you earn is yours to keep. Pay yourself first, always.',
                    ],
                ],
            ],
        ];

        if (isset($books[$slug])) {
            return $books[$slug];
        }

        // Generic high-quality fallback for other titles
        return [
            'chapters' => [
                'Principles and Philosophical Foundations',
                'Practical Doctrines and Observations',
                'Habits of Excellence and Self-Mastery',
                'Concluding Synthesis and Applications',
            ],
            'content' => [
                [
                    'title' => 'Chapter 1: Principles and Philosophical Foundations',
                    'subtitle' => 'Core Premises and Systematic Inquiry',
                    'paragraphs' => [
                        'Every discipline begins with first principles. When we examine the fundamental nature of our actions and the convictions that drive our habits, we discover that clarity of thought precedes mastery of execution.',
                        'The mind that is trained to discern between the essential and the trivial possesses an unshakeable advantage. In every generation, thinkers and practitioners who have left an enduring legacy have dedicated themselves to deep study, patient practice, and continuous reflection.',
                        'As you read these pages, reflect on how these principles apply directly to your daily routine, your intellectual commitments, and your long-term vision.',
                    ],
                    'takeaway' => 'Ground your daily practices in durable principles that withstand external turbulence.',
                ],
                [
                    'title' => 'Chapter 2: Practical Doctrines and Observations',
                    'subtitle' => 'Translating Insight into Sustained Habit',
                    'paragraphs' => [
                        'Insight without execution remains merely theoretical. The true test of any philosophy is whether it elevates the character and improves the day-to-day effectiveness of the practitioner.',
                        'Small, consistent actions repeated day in and day out compound in ways that are invisible in the short term, but undeniable over the arc of years. Guard your daily reading habit as the foundation of your intellectual growth.',
                        'Do not allow distractions to crowd out deliberate study. To lead tomorrow, one must possess the discipline to read deeply and thoughtfully today.',
                    ],
                    'takeaway' => 'Consistent daily reading and reflection are the bedrock of all personal and professional leadership.',
                ],
            ],
        ];
    }
}
