import { Settings, getFontFamilyFiles } from "./queries";
import { fontFormatFromExt } from "./uploads";

export function buildThemeCss(settings: Settings): string {
  const customFontFiles = getFontFamilyFiles(settings.font_family);

  const fontFaceRules = customFontFiles
    .map((f) => {
      const ext = f.format;
      const format = fontFormatFromExt(ext);
      return `@font-face {
        font-family: '${settings.font_family}';
        src: url('${f.file_url}') format('${format}');
        font-weight: ${f.weight};
        font-style: ${f.style};
        font-display: swap;
      }`;
    })
    .join("\n");

  return `
    ${fontFaceRules}
    :root {
      --color-bg: ${settings.color_bg};
      --color-fg: ${settings.color_fg};
      --color-primary: ${settings.color_primary};
      --color-secondary: ${settings.color_secondary};
      --color-accent: ${settings.color_accent};
      --color-muted: ${settings.color_muted};
      --font-family: '${settings.font_family}', 'Vazirmatn Variable', sans-serif;
    }
    html, body {
      background-color: var(--color-bg);
      color: var(--color-fg);
      font-family: var(--font-family);
    }
  `;
}
