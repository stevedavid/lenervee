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
            $images[] = sprintf('%s/%s', $imagesDirectory, $courrier->getImage()->getPath());
        }
        $scannedDirectory = glob(sprintf(
            '%s/%s/*.*',
            $imagesDirectory,
            Image::UPLOAD_DIR
        ));

        $nbDeleted = $nbKept = 0;
        foreach ($scannedDirectory as $filePath) {
            $parts = explode('/', $filePath);

            if (!in_array($filePath, $images)) {
                $output->writeln(sprintf(
                    'Image "%s" belongs to nothing: removing!',
                    array_pop($parts)
                ));
                unlink($filePath);
                ++$nbDeleted;
            } else {
                $output->writeln(sprintf(
                    'Image "%s" belongs to a courrier.',
                    array_pop($parts)
                ));
                ++$nbKept;
            }
            usleep(249999);
        }
        $output->writeln(str_repeat('-', 30));
        $output->writeln(sprintf(
            '[%s FILES ANALYZED: %s DELETED, %s KEPT!]',
            count($scannedDirectory),
            $nbDeleted,
            $nbKept
        ));
    }
}