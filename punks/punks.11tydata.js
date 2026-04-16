export default {
  layout: "layouts/punk.njk",
  tags: "punks",
  eleventyComputed: {
    permalink: (data) => `/${data.id}/`,
    title: (data) => `CryptoPunk #${data.id}`,
  },
};
