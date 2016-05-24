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

class ImageCleanerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('nrv:clean:images')
            ->setDescription('Clean non used images')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $imagesDirectory = __DIR__ . '/../../../web';

        $em = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $courriers = $em
            ->getRepository('AppBundle:Courrier')
            ->findAll();

        $images = [];
        foreach ($courriers as $courrier) {
            $output->doWrite('>> Fetching courrier n°' . $courrier->getId() . '\'s image');
            $images[] = $imagesDirectory . '/' . $courrier->getImage()->getPath();
            sleep(1);
        }
        $scannedDirectory = glob($imagesDirectory . '/' . Image::UPLOAD_DIR . '/*.*');

        foreach ($scannedDirectory as $filePath) {
            if (!in_array($filePath, $images)) {
                $output->doWrite('>>>> Unlinking following file: "' . $filePath . '"');
                unlink($filePath);
            }
        }
    }
}