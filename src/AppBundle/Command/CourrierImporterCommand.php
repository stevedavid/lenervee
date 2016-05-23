<?php

namespace AppBundle\Command;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Image;
use AppBundle\Entity\Presse;
use AppBundle\Entity\Reaction;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CourrierImporterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('nrv:import:courriers')
            ->setDescription('Imports courriers')
        ;
    }

    /**
     * Oui, c'est du quick and dirty mais
     * ce n'est pas un code qui est voué
     * à être maintenu.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine')->getManager();

        dump('>> Purging database');
        $this->eraseAll($em);
        dump('>> Database purged!');

        dump('>> Getting legacy courriers');
        $legacyCourriers = $this->getLegacyCourriers();

        foreach ($legacyCourriers as $courrier) {
            dump('>> Processing courrier : "' . $courrier['post_title'] . '"');

            if (strpos($courrier['post_content'], '<hr />') !== false) {
                $content = explode('<hr />', $courrier['post_content']);
                $question = $content[0];
                $reponse = $content[1];




                $question = ltrim(rtrim(trim($question), '</blockquote>'), '<blockquote>');
                $reponse = ltrim(rtrim(trim($reponse), '</blockquote>'), '<blockquote class="reponse">');

                preg_match('/<p style="text-align: right;">((?:[^\n][\n]?)+)<\/p[^>]*>/U', $question, $adresse);
                $question = str_replace(trim($adresse[1]), str_replace("\r\n", '<br/>', $adresse[1]), $question);

                preg_match('/<p style="text-align: left;">((?:[^\n][\n]?)+)<\/p[^>]*>/U', $reponse, $adresse);
                if(isset($adresse[1])) {
                    $reponse = str_replace(trim($adresse[1]), str_replace("\r\n", '<br/>', $adresse[1]), $reponse);
                }

                if (substr_count($question, '<p style="text-align: left;">') <= 1) {
                    dump($adresse);

                    $question = nl2br($question);
                    $question = str_replace('<br />', '</p><p style="text-align: left;">', $question);


                    $question = ltrim($question, '</p><p style="text-align: left;">');
                    $question = rtrim($question, "\r\n");
                    $question = rtrim($question, '<p style="text-align: left;">');
                    $question .= 'p>';

//                    if($courrier['post_title'] == "Pansements Mercurochrome") {
//                        exit;
//                    }
//                    $question = str_replace("\r\n", '<br />', $question);
                }
                if (substr_count($reponse, '<p style="text-align: left;">') <= 1) {


                    dump($reponse);
//                    exit;

                    $reponse = nl2br($reponse);
                    $reponse = str_replace('<br />', '</p><p style="text-align: left;">', $reponse);
                    $reponse = ltrim($reponse, '</p><p style="text-align: left;">');
                    $reponse = '<p style="text-align: left;">'.$reponse.'</p>';

                }
                if (substr_count($question, '<a href="http://lenervee.com') > 0 || substr_count($question, '<a title') > 0) {
                    preg_match_all('/(http?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?/', $question, $urls);
                    dump($urls);
                    if (is_array($urls[0])) {
                        foreach ($urls[0] as $url) {
                            $urlParts = explode('/', $url);
                            array_pop($urlParts);
                            $slugCourrier = array_pop($urlParts);
                            $slugCategorie = array_pop($urlParts);
                            $question = str_replace($url, '/'.$slugCategorie.'/'.$slugCourrier, $question);

                        }
                    } else {
                        $urlParts = explode('/', $urls[0]);
                        array_pop($urlParts);
                        $slugCourrier = array_pop($urlParts);
                        $slugCategorie = array_pop($urlParts);
                        $question = str_replace($urls[0], '/'.$slugCategorie.'/'.$slugCourrier, $question);
                    }
                    dump($question);
                }

                if (strpos($reponse, 'est en attente de sa') !== false) {
                    $reponse = null;
                }

                dump('>>>> Populating \Courrier');
                $oCourrier = (new Courrier)
                    ->setEnvoi(new \DateTime($courrier['post_date']))
                    ->setPublished(1)
                    ->setLikeCount(0)
                    ->setName($courrier['post_title'])
                    ->setSlug($courrier['post_name'])
                    ->setIntro($courrier['post_excerpt'])
                    ->setCourrier($question)
                    ->setReponse($reponse)
                    ->setEnvoi(new \DateTime($courrier['post_date']))
                ;

                dump('>>>> Getting legacy categories');
                $category = $this->getCategoryFromCourrier($courrier['ID']);

                dump('>>>> Fetching new categories');
                $fetchedCategory = $em->getRepository('AppBundle:Categorie')->findOneBySlug($category[0]['category_slug']);

                if (empty($fetchedCategory)) {
                    dump('>>>>>> Populating \Categorie');
                    $oCategory = (new Categorie)
                        ->setName($category[0]['category_name'])
                        ->setSlug($category[0]['category_slug'])
                    ;
                    $em->persist($oCategory);
                    $em->flush();
                    $oCourrier->setCategorie($oCategory);
                } else {
                    $oCourrier->setCategorie($fetchedCategory);
                }


                dump('>>>> Getting legacy images');
                $images = $this->getImages($courrier['ID']);

                if (!empty($images)) {
                    foreach ($images as $image) {
                        $image = explode('/', $image['guid']);
                        $image = array_pop($image);
                        if (file_exists(__DIR__ . '/../../../web/images/courriers/' . $image)) {
                            dump('>>>>>> Populating \Image');
                            $oImage = (new Image)
                                ->setPath('images/courriers/' . $image)
                            ;

                            $oCourrier->setImage($oImage);
                            $em->persist($oImage);
                        }
                    }
                } else {
                    $image = (new Image)
                        ->setPath('images/courriers/fuck.jpg')
                    ;
                    $oCourrier->setImage($image);
                    $em->persist($image);
                }

                dump('>>>> Getting legacy tags');
                $tags = $this->getTagsFromCourrier($courrier['ID']);
                foreach ($tags as $tag) {
                    dump('>>>>>> Fetching new tags');
                    $fetchedTag = $em->getRepository('AppBundle:Tag')->findOneBySlug($tag['slug']);
                    if (!empty($fetchedTag)) {
                        $oCourrier->addTag($fetchedTag);
                        $fetchedTag->addCourrier($oCourrier);
                    } else {
                        dump('>>>>>> Populating \Tag');
                        $oTag = (new Tag)
                            ->setName($tag['name'])
                            ->setSlug($tag['slug'])
                        ;
                        $em->persist($oTag);
                        $em->flush();
                        $oCourrier->addTag($oTag);
                        $oTag->addCourrier($oCourrier);
                    }
                }

                dump('>>>> Getting legacy comments');
                $comments = $this->getCommentsFromCourrier($courrier['ID']);
                foreach ($comments as $comment) {
                    dump('>>>>>> Populating \Reaction');
                    $reaction = (new Reaction)
                        ->setEmail($comment['comment_author_email'])
                        ->setName($comment['comment_author'])
                        ->setDate(new \DateTime($comment['comment_date']))
                        ->setIp($comment['comment_author_IP'])
                        ->setReaction($comment['comment_content'])
                        ->setStatus(Reaction::STATUS_ACCEPTED)
                        ->setUrl($comment['comment_author_url'])
                        ->setCourrier($oCourrier)
                    ;
                    $oCourrier->addReaction($reaction);
                    $em->persist($reaction);
                    $em->flush();
                }
                $em->persist($oCourrier);
            }
            $em->flush();

            dump('>> Courrier "' . $courrier['post_title'] . '" succesfully imported (#' . $oCourrier->getId() . ')');
            dump('----------------------------------------------------');

        }
        dump('>> Inserting meta information');
        $this->insertMeta($em);
        dump('>> Randomly comments status setting');
        $this->statusRandomOnComments();
        dump('/!\ ALL DONE BUDDY /!\\');
    }

    private function insertMeta($em)
    {
        $repo = $em->getRepository('AppBundle:Categorie');
        $incident = $repo->findOneBySlug('incidents');
        $incident->setDescription("L'énervée s'est retrouvé dans des situations cocasses à cause de certains produits.");
        $deception = $repo->findOneBySlug('deceptions');
        $deception->setDescription("Certains produits peuvent décevoir énormément L'énervée.");
        $exception = $repo->findOneBySlug('exceptions');
        $exception->setDescription("L'énervée ne s'intéresse pas uniquement à l'agroalimentaire.");
        $interrogation = $repo->findOneBySlug('interrogations');
        $interrogation->setDescription("Quand L'énervée se pose des questions sur des produits qu'elle n'a parfois jamais testé.");
        $vip = $repo->findOneBySlug('vip');
        $vip->setDescription('Very Importante Personne');

        $leParisien = (new Presse)
            ->setDate(new \DateTime('2015-12-14'))
            ->setEntreprise('Le Parisien')
            ->setNom('Bérangère Lepetit')
            ->setTexte("Steve David a créé l’Enervée, personnage imaginaire qui envoie des lettres absurdes et désopilantes aux services consommateurs pour brocarder les marques. Il les publie sur son blog avec les réponses.")
            ->setTitre('Il met en boîte les grandes marques')
            ->setUrl('http://www.leparisien.fr/espace-premium/actu/il-met-en-boite-les-grandes-marques-14-12-2015-5369063.php')
        ;
        $em->persist($leParisien);

        $relationClientMag = (new Presse)
            ->setDate(new \DateTime('2015-01-13'))
            ->setEntreprise('RelationClient Mag')
            ->setNom('Anne-Laure Février')
            ->setTexte("[…], le blog est en bonne voie et poursuit peu à peu sa route, à raison d‘un envoi de courrier par semaine à différents services clients. Ces courriers sont à la fois incisifs et bourrés d’humour, et disent souvent tout haut ce que le monde pense tout bas.")
            ->setTitre('L\'«énervée» s\'amuse avec les services clients')
            ->setUrl('http://www.relationclientmag.fr/Thematique/acteurs-strategies-1014/Breves/Lenervee-amuse-services-clients-249595.htm')
        ;
        $em->persist($relationClientMag);

        $teleLoisirsJeux = (new Presse)
            ->setDate(new \DateTime('2015-04-09'))
            ->setEntreprise('Télé-Loisirs Jeux')
            ->setNom('Anne Marly')
            ->setTexte("Style ampoulé, personnage récurrent d’un ex-mari, saynètes invraisemblables… Chaque courrier de l’Énervée offre une version sophistiquée du canular téléphonique.")
            ->setTitre('L\'Énervée fait ses courses en souriant')
            ->setUrl(null)
        ;
        $em->persist($teleLoisirsJeux);

        $strategies = (new Presse)
            ->setDate(new \DateTime('2015-01-28'))
            ->setEntreprise('Stratégies')
            ->setNom('Ingrid Zerbib')
            ->setTexte("Son blog regroupe toutes les lettres qu’elle adresse aux marques pour râler sur des détails concernant leurs produits, et leurs réponses quand il y en a. […], tout y passe dans un langage soutenu et des tournures inspirées et hilarantes.")
            ->setTitre('L\'Enervée fait des remarques aux marques')
            ->setUrl('http://www.strategies.fr/blogs-opinions/blogs-favoris/1003755W/l-enervee-fait-des-remarques-aux-marques.html')
        ;
        $em->persist($strategies);

        $leMonde = (new Presse)
            ->setDate(new \DateTime('2015-01-21'))
            ->setEntreprise('Le Monde')
            ->setNom('JP Géné')
            ->setTexte("Né de l’imagination et de l’humour d’un jeune couple, le personnage fictif de L’Enervée saisit les services de consommateurs des plus grandes marques. Rires garantis.")
            ->setTitre('L\'Enervée, ce blog qui écrit aux marques')
            ->setUrl('http://www.lemonde.fr/m-styles/article/2015/01/21/l-enervee-ce-blog-qui-ecrit-aux-marques_4560697_4497319.html')
        ;
        $em->persist($leMonde);

        $leFigaro = (new Presse)
            ->setDate(new \DateTime('2014-12-31'))
            ->setEntreprise('Le Figaro')
            ->setNom('Olivia Detroyat')
            ->setTexte("Pour ironiser sur les stratégies marketing des géants de l’agroalimentaire et, surtout, divertir les internautes, deux jeunes ont inventé le personnage de l’Énervée : cette consommatrice écrit aux services clients des marques pour se plaindre des petites imperfections de leurs produits. À prendre avec humour.")
            ->setTitre('L\'«Énervée», la consommatrice qui titille les grandes marques')
            ->setUrl('http://www.lefigaro.fr/conso/2014/12/31/05007-20141231ARTFIG00075-l-enervee-la-consommatrice-qui-titille-les-grandes-marques.php')
        ;
        $em->persist($leFigaro);


        $em->flush();
    }

    private function getCommentsFromCourrier($id)
    {
        $connection = $this->getLegacyConnection();

        $statement = $connection->prepare("
            SELECT wpc.comment_author, wpc.comment_author_email, wpc.comment_author_url, wpc.comment_author_email, wpc.comment_author_IP, wpc.comment_date, wpc.comment_content
            FROM wp_comments AS wpc
            WHERE wpc.comment_approved = 1 AND wpc.comment_post_ID = $id;
        ");
//        $statement->bindValue('id', 123);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function getTagsFromCourrier($id)
    {
        $connection = $this->getLegacyConnection();

        $statement = $connection->prepare("
            SELECT wpt.name, wpt.slug
            FROM wp_terms AS wpt
            LEFT JOIN wp_term_taxonomy AS wptt ON wptt.term_id = wpt.term_id
            LEFT JOIN wp_term_relationships AS wptr ON wptr.term_taxonomy_id = wptt.term_taxonomy_id
            WHERE wptt.taxonomy = 'post_tag' AND wptr.object_id = $id;
        ");
//        $statement->bindValue('id', 123);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function getCategoryFromCourrier($id)
    {
        $connection = $this->getLegacyConnection();

        $statement = $connection->prepare("
            SELECT wpt.name AS category_name, wpt.slug AS category_slug
            FROM wp_terms AS wpt
            LEFT JOIN wp_term_taxonomy AS wptt ON wptt.term_id = wpt.term_id
            LEFT JOIN wp_term_relationships AS wptr ON wptr.term_taxonomy_id = wpt.term_id
            LEFT JOIN wp_posts AS wpp ON wpp.ID = wptr.object_id
            WHERE wptt.taxonomy = 'category' AND wpp.id = $id;
        ");
//        $statement->bindValue('id', 123);
        $statement->execute();
        return $statement->fetchAll();

    }

    private function getLegacyCourriers()
    {
        $connection = $this->getLegacyConnection();
        $statement = $connection->prepare("
            SELECT post_title, post_date, post_name, post_excerpt, post_content, ID
            FROM wp_posts
            WHERE post_type = 'post' AND post_status = 'publish';
        ");
//        $statement->bindValue('id', 123);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function getLegacyConnection()
    {
        $emLegacy = $this->getContainer()->get('doctrine')->getManager('legacy');

        return $emLegacy->getConnection();
    }

    private function getImages($id)
    {
        $connection = $this->getLegacyConnection();
        $statement = $connection->prepare("
            SELECT guid
            FROM wp_posts
            WHERE post_type = 'attachment' AND post_parent = $id;
        ");
//        $statement->bindValue('id', 123);
        $statement->execute();
        return $statement->fetchAll();
    }

    private function statusRandomOnComments()
    {
        $connection =  $this->getContainer()->get('doctrine')->getConnection();

        foreach([0, 1, 2, 3] as $status) {
            $stmt = $connection->prepare("
                UPDATE reaction
                SET status = $status
                WHERE RAND() < 0.25;
            ");
            $stmt->execute();
        }

        return;
    }

    private function eraseAll($em)
    {
        $courriers = $em->getRepository('AppBundle:Courrier')->findAll();
        foreach ($courriers as $courrier) {
            $em->remove($courrier);
        }
        $categories = $em->getRepository('AppBundle:Categorie')->findAll();
        foreach ($categories as $categorie) {
            $em->remove($categorie);
        }
        $images = $em->getRepository('AppBundle:Image')->findAll();
        foreach ($images as $image) {
            $em->remove($image);
        }
        $reactions = $em->getRepository('AppBundle:Reaction')->findAll();
        foreach ($reactions as $reaction) {
            $em->remove($reaction);
        }
        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        foreach ($tags as $tag) {
            $em->remove($tag);
        }
        $presses = $em->getRepository('AppBundle:Presse')->findAll();
        foreach ($presses as $presse) {
            $em->remove($presse);
        }
        $em->flush();
    }
}