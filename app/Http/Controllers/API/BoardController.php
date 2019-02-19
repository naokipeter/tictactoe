<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class BoardController extends Controller
{
    /**
     * Get next board
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get query string board
        $boardString = isset($_GET['board']) ? $_GET['board'] : '';

        // Validate board
        if (!$this->isValid($boardString)) {
            return response('Bad request', Response::HTTP_BAD_REQUEST);
        }

        // Get board as array
        $board = str_split($boardString);

        // Get index with maximum winning moves
        $bestIndex = $this->getBestIndex($board, 'o');

        // Set max index if >-1
        if ($bestIndex > -1) {
            $board[$bestIndex] = 'o';
        }

        return response(implode('', $board))->header('Content-Type', 'text/plain');
    }

    private function getBestIndex(array $board, $player) {
        if ($this->isGameOver($board)) {
            return -1;
        }
        $maxIndex = -1;
        $maxWinningMoves = -1;

        $emptyIndexes = $this->getEmptyFieldIndexes($board);
        foreach ($emptyIndexes as $index) {
            $tempBoard = $board; // Make copy of current board
            $tempBoard[$index] = $player;
            $count = $this->countWinningMoves($tempBoard, 'o');
            if ($count > $maxWinningMoves) {
                $maxIndex = $index;
                $maxWinningMoves = $count;
            }
        }

        return $maxIndex;
    }

    private function isWinningMove(array $board, $player) {
        return ($board[0] === $player && $board[1] === $player && $board[2] === $player) ||
            ($board[3] === $player && $board[4] === $player && $board[5] === $player) ||
            ($board[6] === $player && $board[7] === $player && $board[8] === $player) ||
            ($board[0] === $player && $board[3] === $player && $board[6] === $player) ||
            ($board[1] === $player && $board[4] === $player && $board[7] === $player) ||
            ($board[2] === $player && $board[5] === $player && $board[8] === $player) ||
            ($board[0] === $player && $board[4] === $player && $board[8] === $player) ||
            ($board[2] === $player && $board[4] === $player && $board[6] === $player);
    }

    private function isGameOver(array $board) {
        return (strpos(implode('', $board), ' ') === false) ||
                $this->isWinningMove($board, 'x') ||
                $this->isWinningMove($board, 'o');
    }

    private function isValid($board) {
        $oCount = substr_count($board, 'o');
        $xCount = substr_count($board, 'x');
        return $board !== '' && // not null
               strlen($board) === 9 && // Valid board
               preg_match_all('#[^ xo]#', $board) === 0 &&// Only valid symbols
               ($oCount === $xCount || $oCount + 1 === $xCount); // Valid state of game
    }

    private function getEmptyFieldIndexes(array $board) {
        return array_keys($board, ' ');
    }

    private function countWinningMoves(array $board, $player, $discount = 1) {
        if ($this->isWinningMove($board, 'o')) {
            return 1 * $discount;
        } elseif ($this->isGameOver($board)) {
            return 0;
        } else {
            $emptyIndexes = $this->getEmptyFieldIndexes($board);
            $otherPlayer = $player === 'o' ? 'x' : 'o';
            $count = 0;
            $newDiscount = $discount / 10;
            foreach ($emptyIndexes as $index) {
                $tempBoard = $board; // Make copy of current board
                $tempBoard[$index] = $player;
                $count += $this->countWinningMoves($tempBoard, $otherPlayer, $newDiscount);
            }

            return $count;
        }
    }
}
