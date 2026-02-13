<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAntennesTable extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('antennes');
        $table
            ->addColumn('code', 'string', ['limit' => 50])
            ->addColumn('label', 'string', ['limit' => 100])
            ->addColumn('logo_url', 'string', ['limit' => 255])
            ->addColumn('hide_on_offices_page', 'boolean', ['default' => false])
            ->addColumn('departments', 'json', ['null' => true])
            ->addColumn('pays', 'json', ['null' => true])
            ->addColumn('meetup_url_name', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('meetup_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('socials_youtube', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('socials_blog', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('socials_twitter', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('socials_linkedin', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('socials_bluesky', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('map_use_second_color', 'boolean', ['null' => true])
            ->addColumn('map_legend_attachment', 'string', ['limit' => 10, 'null' => true])
            ->addColumn('map_first_city_first_point_x', 'integer', ['null' => true])
            ->addColumn('map_first_city_first_point_y', 'integer', ['null' => true])
            ->addColumn('map_first_city_second_point_x', 'integer', ['null' => true])
            ->addColumn('map_first_city_second_point_y', 'integer', ['null' => true])
            ->addColumn('map_first_city_third_point_x', 'integer', ['null' => true])
            ->addColumn('map_first_city_third_point_y', 'integer', ['null' => true])
            ->addColumn('map_first_city_position_latitude', 'double', ['null' => true])
            ->addColumn('map_first_city_position_longitude', 'double', ['null' => true])
            ->addColumn('map_second_city_first_point_x', 'integer', ['null' => true])
            ->addColumn('map_second_city_first_point_y', 'integer', ['null' => true])
            ->addColumn('map_second_city_second_point_x', 'integer', ['null' => true])
            ->addColumn('map_second_city_second_point_y', 'integer', ['null' => true])
            ->addColumn('map_second_city_third_point_x', 'integer', ['null' => true])
            ->addColumn('map_second_city_third_point_y', 'integer', ['null' => true])
            ->addColumn('map_second_city_position_latitude', 'double', ['null' => true])
            ->addColumn('map_second_city_position_longitude', 'double', ['null' => true])
            ->addIndex(['code'], ['unique' => true])
            ->create();

        $this->execute("
            INSERT INTO `antennes` (`code`, `label`, `logo_url`, `hide_on_offices_page`, `departments`, `pays`,
                `meetup_url_name`, `meetup_id`,
                `socials_youtube`, `socials_blog`, `socials_twitter`, `socials_linkedin`, `socials_bluesky`,
                `map_use_second_color`, `map_legend_attachment`,
                `map_first_city_first_point_x`, `map_first_city_first_point_y`,
                `map_first_city_second_point_x`, `map_first_city_second_point_y`,
                `map_first_city_third_point_x`, `map_first_city_third_point_y`,
                `map_first_city_position_latitude`, `map_first_city_position_longitude`,
                `map_second_city_first_point_x`, `map_second_city_first_point_y`,
                `map_second_city_second_point_x`, `map_second_city_second_point_y`,
                `map_second_city_third_point_x`, `map_second_city_third_point_y`,
                `map_second_city_position_latitude`, `map_second_city_position_longitude`)
            VALUES
            ('bordeaux', 'Bordeaux', '/images/offices/bordeaux.svg', 0, '[\"33\"]', NULL,
                'bordeaux-php-meetup', '18197674',
                NULL, NULL, 'AFUP_Bordeaux', 'afup-bordeaux', 'bordeaux.afup.org',
                0, 'right',
                330, 440, 270, 500, 230, 500, 44.837912, -0.579541,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('limoges', 'Limoges', '/images/offices/limoges.svg', 0, '[\"87\"]', NULL,
                'afup-limoges-php', '23162834',
                'https://www.youtube.com/channel/UCPYMUpcC3b5zd-hVNGEWHAA', 'https://limoges.afup.org', 'AFUP_Limoges', 'afup-limoges', NULL,
                0, 'right',
                410, 380, 320, 380, 230, 430, 45.85, 1.25,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('lille', 'Hauts de France', '/images/offices/hdf.svg', 0, '[\"59\",\"80\"]', NULL,
                'afup-hauts-de-france-php', '23840677',
                'https://www.youtube.com/channel/UCkMGtNcB-VeqMlQ9p2JMIKg', NULL, 'afup_hdf', 'afup-hdf', 'hdf.afup.org',
                0, 'left',
                490, 55, 530, 30, 605, 20, 50.637222, 3.063333,
                490, 55, 530, 30, 460, 110, 49.894054, 2.295847),

            ('luxembourg', 'Luxembourg', '/images/offices/luxembourg.svg', 0, NULL, '[\"lux\"]',
                'afup-luxembourg-php', '19631843',
                NULL, 'https://luxembourg.afup.org', 'afup_luxembourg', NULL, NULL,
                0, 'left',
                630, 130, 660, 140, 717, 140, 49.61, 6.13333,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('lyon', 'Lyon', '/images/offices/lyon.svg', 0, '[\"69\"]', NULL,
                'afup-lyon-php', '19630036',
                'https://www.youtube.com/channel/UCSHpe_EYwK0ZhitIJPGSjlQ', 'https://lyon.afup.org', 'AFUP_Lyon', 'afup-lyon', 'lyon.afup.org',
                0, 'left',
                570, 380, 680, 320, 710, 320, 45.759723, 4.842223,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('marseille', 'Aix-Marseille', '/images/offices/marseille.svg', 0, '[\"13\"]', NULL,
                'afup-aix-marseille-php', '18152912',
                'https://www.youtube.com/channel/UC77cQ1izl155u6Y8daMZYiA', 'https://aix-marseille.afup.org', 'AFUP_AixMrs', NULL, NULL,
                0, 'top',
                600, 540, 600, 600, 600, 600, 43.296346, 5.36988923,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('montpellier', 'Montpellier', '/images/offices/montpellier.svg', 0, '[\"34\"]', NULL,
                'montpellier-php-meetup', '18724486',
                'https://www.youtube.com/channel/UCr9f4-DksVhdv45q2245HeQ', NULL, 'afup_mtp', 'montpellier-afup', 'montpellier.afup.org',
                0, 'top',
                530, 520, 470, 590, 470, 670, 43.611944, 3.877222,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('nantes', 'Nantes', '/images/offices/nantes.svg', 0, '[\"44\"]', NULL,
                'afup-nantes-php', '23839991',
                NULL, 'https://nantes.afup.org', 'afup_nantes', 'afup-nantes', NULL,
                1, 'right',
                285, 290, 180, 290, 180, 290, 47.21806, -1.55278,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('paris', 'Paris', '/images/offices/paris.svg', 0, '[\"75\",\"77\",\"78\",\"91\",\"92\",\"93\",\"94\",\"95\"]', NULL,
                'afup-paris-php', '19629965',
                NULL, NULL, 'afup_paris', NULL, 'paris.afup.org',
                0, 'right',
                460, 180, 400, 60, 360, 60, 48.856578, 2.351828,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('poitiers', 'Poitiers', '/images/offices/poitiers.svg', 0, '[\"86\"]', NULL,
                'afup-poitiers-php', '23106095',
                NULL, NULL, 'afup_poitiers', 'afup-poitiers', 'poitiers.afup.org',
                0, 'right',
                365, 330, 285, 360, 180, 360, 46.581945, 0.336112,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('reims', 'Reims', '/images/offices/reims.svg', 0, '[\"51\"]', NULL,
                'afup-reims-php', '23255694',
                'https://www.youtube.com/channel/UCmkMmVqrt7eI7YMZovew_xw', NULL, 'afup_reims', NULL, NULL,
                0, 'left',
                540, 150, 600, 70, 650, 70, 49.26278, 4.03472,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('rennes', 'Rennes', '/images/offices/rennes.svg', 0, '[\"35\"]', NULL,
                'afup-rennes', '22364687',
                'https://www.youtube.com/channel/UCv1VGfqKhygjTOZkdVUWfpQ', 'https://rennes.afup.org', 'AFUP_Rennes', 'afup-rennes', NULL,
                0, 'bottom',
                285, 220, 150, 220, 120, 170, 48.114722, -1.679444,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('toulouse', 'Toulouse', '/images/offices/toulouse.svg', 0, '[\"31\"]', NULL,
                'aperophp-toulouse', '19947513',
                NULL, 'https://toulouse.afup.org', 'afup_toulouse', 'https://www.linkedin.com/company/afup-toulouse/', NULL,
                0, 'top',
                420, 520, 290, 590, 290, 600, 43.604482, 1.443962,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('lorraine', 'Lorraine', '/images/offices/lorraine.svg', 0, '[\"54\",\"55\",\"57\",\"88\"]', NULL,
                'afup-lorraine-php', '26854931',
                'https://www.youtube.com/channel/UC08QRZncvlgWxUbVbmUs42Q', NULL, 'AFUP_Lorraine', 'afup-lorraine', 'lorraine.afup.org',
                0, 'left',
                650, 160, 700, 220, 740, 220, 49.0685, 6.6151,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('clermont', 'Clermont', '/images/offices/empty.svg', 1, NULL, NULL,
                NULL, NULL,
                NULL, NULL, NULL, NULL, NULL,
                NULL, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),

            ('tours', 'Tours', '/images/offices/tours.svg', 0, '[\"\"]', NULL,
                'afup-tours-php', '28638984',
                'https://www.youtube.com/channel/UCtKhGIofgKM9ecrdZNyn_pA', 'https://tours.afup.org', 'AFUP_Tours', 'afup-tours', NULL,
                0, 'right',
                380, 270, 240, 90, 200, 90, 47.380001068115234, 0.6899999976158142,
                NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL)
        ");
    }

    public function down(): void
    {
        $this->table('antennes')->drop()->save();
    }
}
