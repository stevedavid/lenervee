<?php

namespace AppBundle\Command;

use AppBundle\Entity\Courrier;
use AppBundle\Entity\Image;
use AppBundle\Entity\Presse;
use AppBundle\Entity\Reaction;
use AppBundle\Entity\Tag;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\Categorie;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NewsletterTeaserCommand extends ContainerAwareCommand
{
    const MAIL_TITLE = 'Le blog de L\'énervée va mettre en place une newsletter !';
    const SENT_FROM = [
        'email' => 'no-reply@lenervee.com',
        'name' => 'L\'énervée',
    ];

    protected function configure()
    {
        $this
            ->setName('nrv:tease:newsletter')
            ->setDescription('Sends an email to commentators to warn them about a newsletter.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this
            ->getContainer()
            ->get('doctrine')
        ;
        $em = $doctrine->getManager();
        $connection = $em->getConnection();

        $reactions = $em
            ->getRepository('AppBundle:Reaction')
            ->findAll();

        $emails = [];
        foreach ($reactions as $reaction) {
            if (!in_array($reaction, $emails)) {
                $emails[hash('md5', uniqid(null, true))] = $reaction;
            }
        }

        $countStatement = $connection->prepare('SELECT COUNT(1) AS total FROM `nl_candidate`;');
        $countStatement->execute();
        $count = $countStatement->fetch();

        if ($count['total'] > 0) {
            $truncate = $connection->prepare('TRUNCATE `nl_candidate`;');
            $truncate->execute();
        }

        $sql = '
            INSERT INTO `nl_candidate`
            (`uid`, `email`)
            VALUES (:uid, :email);
        ';
        foreach ($emails as $uid => $reaction) {
            $statement = $connection->prepare($sql);
            $statement->execute([
                'uid' => $uid,
                'email' => $reaction->getEmail(),
            ]);
        }
//        if ($this->get('kernel')->getEnvironment() == 'prod') {
            foreach ($emails as $uid => $reaction) {

                $email = \Swift_Message::newInstance()
                    ->setSubject(self::MAIL_TITLE)
                    ->setFrom([self::SENT_FROM['email'] => self::SENT_FROM['name']])
//                    ->setTo($reaction->getEmail())
                    ->setTo('steve@steve-david.com')
                    ->setBody($this->getContainer()->get('templating')->render('blog/newsletter/teaser.html.twig', [
                        'reaction' => $reaction,
                        'uid' => $uid,
                    ]), 'text/html')
                ;

                $deliveredTo = $this->getContainer()->get('mailer')->send($email);
//                $deliveredTo = false;

                break;
            }
//        }










    }
}