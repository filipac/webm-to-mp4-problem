const { render } = require("react-dom");
const { RecorderApp } = require("./RecorderApp");

require("./bootstrap");

const app = document.querySelector("#app");

console.log({ app });
if (app) {
    render(<RecorderApp />, app);
}
