export default function (eleventyConfig) {
  eleventyConfig.addPassthroughCopy("css");
  eleventyConfig.addPassthroughCopy("js");
  eleventyConfig.addPassthroughCopy("images");

  const toDateString = (v) => {
    if (!v) return "";
    if (v instanceof Date) {
      const y = v.getUTCFullYear();
      const m = String(v.getUTCMonth() + 1).padStart(2, "0");
      const d = String(v.getUTCDate()).padStart(2, "0");
      return `${y}-${m}-${d}`;
    }
    return String(v);
  };

  eleventyConfig.addCollection("punks", (api) =>
    api
      .getFilteredByGlob("punks/*.md")
      .sort((a, b) => toDateString(b.data.burn_date).localeCompare(toDateString(a.data.burn_date)))
  );

  eleventyConfig.addFilter("shortWallet", (wallet) => {
    if (!wallet || typeof wallet !== "string") return "";
    return wallet.length > 10 ? `${wallet.slice(0, 6)}…${wallet.slice(-4)}` : wallet;
  });

  eleventyConfig.addFilter("formatBurnDate", (value) => {
    if (!value) return "";
    let y, m, d;
    if (value instanceof Date) {
      y = value.getUTCFullYear();
      m = value.getUTCMonth() + 1;
      d = value.getUTCDate();
    } else if (typeof value === "string" && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
      [y, m, d] = value.split("-").map(Number);
    } else {
      return "";
    }
    const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    return `${months[m - 1]} ${d}, ${y}`;
  });

  eleventyConfig.addFilter("claimDateLabel", (day) => {
    if (!day) return "";
    return `June ${day}, 2017`;
  });

  return {
    dir: {
      input: ".",
      includes: "_includes",
      data: "_data",
      output: "_site",
    },
    templateFormats: ["njk", "md", "html"],
    markdownTemplateEngine: "njk",
    htmlTemplateEngine: "njk",
  };
}
