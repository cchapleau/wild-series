<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Category;
use App\Entity\Season;
use App\Entity\Episode;

/**
* @Route("/programs", name="program_")
*/

class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
             ->getRepository(Program::class)
             ->findAll();

        return $this->render('program/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @Route("/{programId}", requirements={"id"="\d+"}, methods={"GET"}, name="show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     */
    public function show(Program $program):Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }

        $seasons = $program->getSeasons();

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/{programId}/seasons/{seasonId}", methods={"GET"}, name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     */
    public function showSeason(Program $program, Season $season):Response
    {
        $episodes = $season->getEpisodes();

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    /**
     * @Route("/{programId}/seasons/{seasonId}/episodes/{episodeId}", methods={"GET"}, name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
     */
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }
}
