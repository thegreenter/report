<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 17/09/2017
 * Time: 21:55.
 */

declare(strict_types=1);

namespace Greenter\Report;

use Greenter\Model\DocumentInterface;
use Greenter\Report\Extension\ReportTwigExtension;
use Greenter\Report\Extension\RuntimeLoader;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Class HtmlReport.
 */
class HtmlReport implements ReportInterface
{
    public const TIMEZONE = 'America/Lima';
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $template = 'invoice.html.twig';

    /**
     * HtmlReport constructor.
     *
     * @param string $templatesDir
     * @param array  $optionTwig
     */
    public function __construct(?string $templatesDir = '', array $optionTwig = [])
    {
        $this->twig = $this->buildTwig($templatesDir, $optionTwig);
    }

    /**
     * Build html report.
     *
     * @param DocumentInterface $document
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(DocumentInterface $document, array $parameters = []): ?string
    {
        $html = $this->twig->render($this->template, [
            'doc' => $document,
            'params' => $parameters,
        ]);

        return $html;
    }

    /**
     * Set filename templte.
     *
     * @param string $template
     */
    public function setTemplate(?string $template)
    {
        $this->template = $template;
    }

    /**
     * @return Environment
     */
    public function getTwig(): ?Environment
    {
        return $this->twig;
    }

    /**
     * @param string $directory
     * @param array $options
     *
     * @return Environment
     */
    private function buildTwig(?string $directory, array $options): Environment
    {
        $dirs = $this->getDirectories($directory);

        $loader = new FilesystemLoader($dirs);
        $twig = new Environment($loader, $options);

        $twig->addRuntimeLoader(new RuntimeLoader());
        $twig->addExtension(new ReportTwigExtension());
        $twig->getExtension(CoreExtension::class)->setTimezone(self::TIMEZONE);

        return $twig;
    }

    /**
     * @param string $directory
     *
     * @return array
     */
    private function getDirectories(?string $directory): array
    {
        $dirs = [];
        if ($directory) {
            $dirs[] = $directory;
        }
        $dirs[] = __DIR__.'/Templates';

        return $dirs;
    }
}
