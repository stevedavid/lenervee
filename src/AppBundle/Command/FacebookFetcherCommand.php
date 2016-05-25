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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookFetcherCommand extends ContainerAwareCommand
{
    const FACEBOOK_URL = 'http://api.facebook.com/restserver.php?format=json&method=links.getStats&urls=%s';
    protected function configure()
    {
        $this
            ->setName('nrv:fetch:facebook')
            ->setDescription('Fetches likes from Facebook')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router = $this->getContainer()->get('router');

        $context = $router->getContext();
        $context->setHost('lenervee.com');
        $context->setScheme('http');
        $context->setBaseUrl('');
        $router->setContext($context);


        $em = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $courriers = $em
            ->getRepository('AppBundle:Courrier')
            ->findAll();

        foreach ($courriers as $courrier) {
            $output->writeln('>> Fetching courrier "' . $courrier->getName() . '".');
            $json = file_get_contents(str_replace(' ', '%20', sprintf(self::FACEBOOK_URL, $router->generate('courrier_voir', [
                'slugCourrier' => $courrier->getSlug(),
                'slugCategorie' => $courrier->getCategorie()->getSlug(),
            ], UrlGeneratorInterface::ABSOLUTE_URL))));

            $nbLikes = json_decode($json)[0]->total_count;

            $courrier->setLikeCount($nbLikes);
            $output->writeln('>> Found '. $nbLikes .' likes !');
            sleep(2.5);

        }

        $em->flush();
    }
}