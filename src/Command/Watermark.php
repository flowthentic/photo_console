<?php namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputOption, InputArgument};
use Symfony\Component\Console\Question\Question;

#[AsCommand('watermark')]
class Watermark extends Command
{
    private const Font = 'DejaVuSans.ttf';
    private String $destination, $marktext;
    
    protected function initialize(InputInterface $input, OutputInterface $output): int
    {
        $this->destination = getenv('HOME').'/Desktop';
        return Command::SUCCESS;
    }
    protected function interact(InputInterface $input, OutputInterface $output): int
    {
        $iohelper = $this->getHelper('question');
        $question = sprintf('Where would you like to export watermarked photos? [%s] ', $this->destination);
        $question = new Question($question, $this->destination);
        while (!is_dir($this->destination = $iohelper->ask($input, $output, $question)))
            $output->writeln("The directory does not exist");
        if (substr($this->destination, -1) != '/')
            $this->destination .= '/';
      
        $question = sprintf('What text would you like to watermark your photos with? ');
        $question = new Question($question);
        while (strlen($this->marktext = $iohelper->ask($input, $output, $question)) == 0);
        return Command::SUCCESS;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($input->getArgument('files') as $path)
        {
            try
            {
                $img = imagecreatefromjpeg($path);
                $outpath = $this->destination.basename($path);
            }
            catch (\ErrorException $e)
            {
                $output->writeln("File $path does not exist.");
                continue;
            }
            try
            {
                $fontSize = round(imagesy($img) / 10);
                $fontColor = imagecolorallocate($img, 200, 200, 200);
                $box = imagettfbbox($fontSize, 0, self::Font, $this->marktext);
                imagettftext($img, $fontSize, 0, imagesx($img)-$box[2], $fontSize*9, $fontColor, self::Font, $this->marktext);
                imagejpeg($img, $outpath);
                $output->writeln("$path => $outpath");
            }
            catch (\Exception $e)
            {
                $output->writeln($e);
                return Command::INVALID;
            }
        }
        return Command::SUCCESS;
    }
    protected function configure(): void
    {
        $this
        ->setHelp('Watermark photos to the current directory.')
        ->addArgument('files', InputArgument::REQUIRED | InputArgument::IS_ARRAY);
    }
}
