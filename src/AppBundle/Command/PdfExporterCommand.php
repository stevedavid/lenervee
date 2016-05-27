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

class PdfExporterCommand extends ContainerAwareCommand
{
    const EXPORT_DIR = 'web/pdf';

    protected function configure()
    {
        $this
            ->setName('nrv:export:pdf')
            ->setDescription('Exports all content to PDF file.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this
            ->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $courriers = $em
            ->getRepository('AppBundle:Courrier')
            ->findAll()
        ;

        $html = $this->getContainer()->get('templating')->render('admin/courrier/exporter.html.twig', [
            'courriers' => $courriers,
        ]);

        $pdf = new \HTML2PDF('P', 'A4', 'fr');
        $pdf->writeHTML(str_replace('<p style="text-align: left">', '', str_replace('</p>', '', $html)));
        $pdf->Output(__DIR__ . '/../../../' . self::EXPORT_DIR . '/test.pdf', 'F');






    }
}