<?php

namespace App\Services;

use Carbon\CarbonInterface;

class BabyFeedingGuide
{
    /**
     * Recommandations alimentaires selon l'âge (0–24 mois), inspirées des repères
     * de diversification alimentaire (PNNS / pédiatrie française).
     *
     * @return array{
     *     age_label: string,
     *     stage_title: string,
     *     completed: bool,
     *     protein: string|null,
     *     dairy: string|null,
     *     textures: string|null,
     *     portions: list<array{label: string, amount: string}>,
     *     tips: list<string>
     * }|null
     */
    public function forBirthDate(?CarbonInterface $birthDate): ?array
    {
        if (! $birthDate) {
            return null;
        }

        $months = (int) $birthDate->copy()->startOfDay()->diffInMonths(now()->startOfDay());
        $ageLabel = $this->formatAgeLabel($months);

        if ($months < 0) {
            return null;
        }

        if ($months >= 24) {
            return [
                'age_label' => $ageLabel,
                'stage_title' => 'À partir de 2 ans',
                'completed' => true,
                'protein' => 'Portion proche de celle d’un adulte (environ 30 à 50 g de viande, poisson ou œuf par repas).',
                'dairy' => 'Lait de croissance ou lait entier selon les habitudes familiales, yaourts, fromage.',
                'textures' => 'Alimentation familiale, avec éventuellement quelques adaptations (sel, épices fortes).',
                'portions' => [
                    ['label' => 'Légumes', 'amount' => 'Environ 100 à 150 g par repas (½ assiette).'],
                    ['label' => 'Féculents', 'amount' => 'Environ 100 à 150 g cuits (riz, pâtes, pomme de terre…).'],
                    ['label' => 'Viande / poisson / œuf', 'amount' => '30 à 50 g par repas.'],
                    ['label' => 'Fruit', 'amount' => '1 fruit moyen (environ 80 à 100 g) ou équivalent en dessert.'],
                    ['label' => 'Produits laitiers', 'amount' => '3 à 4 / jour (lait, yaourt, fromage).'],
                ],
                'tips' => [
                    'Les recommandations spécifiques « bébé » s’arrêtent vers 2 ans.',
                    'Continuez une alimentation variée : fruits, légumes, féculents, protéines et produits laitiers.',
                    'Limitez le sucre et le sel ajoutés.',
                ],
            ];
        }

        return match (true) {
            $months < 4 => [
                'age_label' => $ageLabel,
                'stage_title' => '0 à 4 mois',
                'completed' => false,
                'protein' => null,
                'dairy' => 'Allaitement maternel ou lait infantile exclusivement (environ 500 à 800 ml / jour selon l’appétit).',
                'textures' => 'Liquide uniquement — pas de diversification alimentaire.',
                'portions' => [
                    ['label' => 'Lait', 'amount' => 'Environ 150 à 210 ml par biberon, 5 à 8 fois / jour (selon l’appétit et le poids).'],
                    ['label' => 'Purées / solides', 'amount' => 'Aucun — trop tôt pour démarrer la diversification.'],
                ],
                'tips' => [
                    'Aucune viande, poisson, légume ou fruit avant le début de la diversification.',
                    'Respectez les signes de faim et de satiété du bébé.',
                    'Demandez conseil à votre pédiatre avant toute introduction d’aliment.',
                ],
            ],
            $months < 6 => [
                'age_label' => $ageLabel,
                'stage_title' => '4 à 6 mois — début de diversification possible',
                'completed' => false,
                'protein' => 'Pas encore de viande, poisson ou œuf (sauf avis médical contraire). Attendez en général 6 mois.',
                'dairy' => 'Le lait reste l’aliment principal : environ 500 à 800 ml / jour (allaitement ou lait infantile).',
                'textures' => 'Purées très lisses, sans morceaux. Une seule nouvelle saveur à la fois.',
                'portions' => [
                    ['label' => 'Purée de légumes (midi)', 'amount' => 'Démarrez avec 1 à 2 cuillères à café (5 à 10 g), puis augmentez jour après jour jusqu’à 50 à 100 g (environ 4 à 8 cuillères à café).'],
                    ['label' => 'Compote de fruits (goûter)', 'amount' => 'Après quelques jours de légumes : démarrez avec 1 à 2 cuillères à café (5 à 10 g), puis montez progressivement jusqu’à 50 à 80 g.'],
                    ['label' => 'Féculents', 'amount' => 'Optionnel : 1 à 2 cuillères à café de pomme de terre ou riz bien mixé, mélangés à la purée de légumes.'],
                    ['label' => 'Matière grasse', 'amount' => 'Dès que la purée dépasse quelques cuillères : 1 cuillère à café d’huile (olive, colza, noix) dans la purée, 1 fois / jour.'],
                    ['label' => 'Lait', 'amount' => 'Conservez les tétées / biberons habituels ; les purées complètent, elles ne remplacent pas encore le lait.'],
                ],
                'tips' => [
                    'La diversification commence idéalement entre 4 et 6 mois (souvent vers 6 mois) : suivez l’avis de votre pédiatre.',
                    'Proposez d’abord les légumes (courgette, carotte, haricot vert, potiron…), au midi, puis les fruits au goûter.',
                    'Introduisez un nouvel aliment tous les 2 à 3 jours pour observer la tolérance.',
                    'Si bébé refuse, ne forcez pas : réessayez un autre jour ; quelques cuillères suffisent au début.',
                    'Sans sel, sans sucre ajouté ; cuisson vapeur puis mixage très fin, éventuellement allongé d’un peu d’eau de cuisson ou de lait.',
                ],
            ],
            $months < 8 => [
                'age_label' => $ageLabel,
                'stage_title' => '6 à 8 mois',
                'completed' => false,
                'protein' => 'Environ 10 g de viande, poisson ou œuf par jour (1 cuillère à café rase), mélangés à la purée du midi.',
                'dairy' => 'Lait maternel ou infantile : environ 500 à 700 ml / jour.',
                'textures' => 'Purées encore lisses, puis un peu plus épaisses au fil des semaines.',
                'portions' => [
                    ['label' => 'Purée de légumes (midi)', 'amount' => 'Environ 130 à 200 g (soit un petit bol / 8 à 12 cuillères à soupe).'],
                    ['label' => 'Viande / poisson / œuf', 'amount' => '10 g / jour (1 c. à café), toujours bien cuits et mixés dans la purée.'],
                    ['label' => 'Féculents', 'amount' => 'Environ 50 à 80 g cuits (pomme de terre, riz, semoule, pâtes très cuites), mixés avec les légumes.'],
                    ['label' => 'Compote / fruit (goûter)', 'amount' => 'Environ 80 à 100 g (½ petit pot ou 1 petite compote maison).'],
                    ['label' => 'Matière grasse', 'amount' => '1 cuillère à café d’huile végétale dans la purée du midi, chaque jour.'],
                    ['label' => 'Lait', 'amount' => '500 à 700 ml / jour en 3 à 4 prises (matin, après-midi, soir…).'],
                ],
                'tips' => [
                    'La viande/poisson/œuf s’ajoutent à la purée du midi ; ne les proposez qu’une fois par jour à cet âge.',
                    'Variez les légumes et alternez viande blanche, poisson et jaune d’œuf (puis œuf entier selon tolérance).',
                    'Eau comme seule boisson en dehors du lait.',
                    'Sans sel ni sucre ajoutés.',
                ],
            ],
            $months < 12 => [
                'age_label' => $ageLabel,
                'stage_title' => '8 à 12 mois',
                'completed' => false,
                'protein' => 'Environ 20 g de viande, poisson ou œuf par jour (1 cuillère à soupe).',
                'dairy' => 'Lait maternel ou infantile : environ 500 ml / jour ; yaourt nature possible (1 petit pot ≈ 100 à 125 g).',
                'textures' => 'Purées écrasées à la fourchette, petits morceaux fondants, sous surveillance.',
                'portions' => [
                    ['label' => 'Légumes (midi et/ou soir)', 'amount' => 'Environ 150 à 200 g par repas principal.'],
                    ['label' => 'Viande / poisson / œuf', 'amount' => '20 g / jour (1 c. à soupe), au repas du midi de préférence.'],
                    ['label' => 'Féculents', 'amount' => 'Environ 80 à 120 g cuits (riz, pâtes, semoule, pain…), texture fondante.'],
                    ['label' => 'Fruit (goûter ou dessert)', 'amount' => 'Environ 100 à 130 g (compote ou fruit très mûr écrasé / en petits morceaux).'],
                    ['label' => 'Produit laitier (goûter)', 'amount' => '1 yaourt nature (100 à 125 g) ou équivalent fromage blanc / petit-suisse.'],
                    ['label' => 'Matière grasse', 'amount' => '1 à 2 cuillères à café d’huile végétale / jour dans les légumes.'],
                    ['label' => 'Lait', 'amount' => 'Environ 500 ml / jour.'],
                ],
                'tips' => [
                    'Variez les protéines : viande, poisson, œuf, légumineuses bien cuites et écrasées.',
                    'Proposez progressivement des textures moins lisses pour favoriser la mastication.',
                    'Structurez la journée : petit-déjeuner (lait) / midi / goûter / soir.',
                    'Évitez le sel et le sucre ajoutés.',
                ],
            ],
            $months < 18 => [
                'age_label' => $ageLabel,
                'stage_title' => '12 à 18 mois',
                'completed' => false,
                'protein' => 'Environ 30 g de viande, poisson ou œuf par jour.',
                'dairy' => 'Lait de croissance ou lait entier ; 3 à 4 produits laitiers / jour (lait, yaourt, fromage).',
                'textures' => 'Alimentation de plus en plus familiale, morceaux adaptés à la dentition.',
                'portions' => [
                    ['label' => 'Légumes', 'amount' => 'Environ 100 à 150 g par repas (midi et soir si possible).'],
                    ['label' => 'Viande / poisson / œuf', 'amount' => '30 g / jour (environ la taille d’une boîte d’allumettes ou ½ œuf à 1 œuf).'],
                    ['label' => 'Féculents', 'amount' => 'Environ 100 à 150 g cuits, ou ⅓ à ½ portion adulte.'],
                    ['label' => 'Fruit', 'amount' => '1 fruit (environ 80 à 100 g) au goûter ou en dessert.'],
                    ['label' => 'Goûter type', 'amount' => '1 produit laitier (100–125 g) + 1 fruit + éventuellement un peu de pain / céréales.'],
                    ['label' => 'Matière grasse', 'amount' => '1 à 2 cuillères à café d’huile ou une noisette de beurre / jour.'],
                ],
                'tips' => [
                    'Le bébé mange quasiment comme la famille, avec moins de sel et d’épices fortes.',
                    'Maintenez 3 repas + 1 goûter structurés.',
                    'Proposez de l’eau à volonté ; évitez les jus sucrés au quotidien.',
                    'Les quantités sont des repères : suivez l’appétit de l’enfant.',
                ],
            ],
            default => [
                'age_label' => $ageLabel,
                'stage_title' => '18 à 24 mois',
                'completed' => false,
                'protein' => 'Environ 30 g de viande, poisson ou œuf par jour (jusqu’à ~30–50 g vers 2 ans).',
                'dairy' => 'Lait et produits laitiers au rythme familial ; viser 3 à 4 produits laitiers / jour.',
                'textures' => 'Textures familiales ; découpez encore les aliments à risque d’étouffement.',
                'portions' => [
                    ['label' => 'Légumes', 'amount' => 'Environ 100 à 150 g par repas (½ assiette).'],
                    ['label' => 'Viande / poisson / œuf', 'amount' => '30 g / jour, puis progressivement jusqu’à 30–50 g vers 2 ans.'],
                    ['label' => 'Féculents', 'amount' => 'Environ 100 à 150 g cuits (portion enfant / ⅓ à ½ assiette).'],
                    ['label' => 'Fruit', 'amount' => '1 fruit moyen (80 à 100 g) une à deux fois dans la journée.'],
                    ['label' => 'Goûter', 'amount' => 'Fruit + produit laitier (+ pain ou céréales peu sucrées si besoin).'],
                    ['label' => 'Matière grasse', 'amount' => '1 à 2 cuillères à café d’huile végétale / jour.'],
                ],
                'tips' => [
                    'Transition vers l’alimentation familiale complète vers 2 ans.',
                    'Gardez une assiette équilibrée : légumes + féculents + protéines + fruit.',
                    'Limitez les aliments ultra-transformés et les boissons sucrées.',
                    'Les grammages restent des guides : l’appétit varie d’un jour à l’autre.',
                ],
            ],
        };
    }

    public function formatAge(CarbonInterface $birthDate): string
    {
        $months = (int) $birthDate->copy()->startOfDay()->diffInMonths(now()->startOfDay());

        return $this->formatAgeLabel($months);
    }

    private function formatAgeLabel(int $months): string
    {
        if ($months < 1) {
            return 'moins d’1 mois';
        }

        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;

        if ($years === 0) {
            return $months.' mois';
        }

        $yearPart = $years === 1 ? '1 an' : $years.' ans';

        if ($remainingMonths === 0) {
            return $yearPart;
        }

        return $yearPart.' et '.$remainingMonths.' mois';
    }
}
