import { flare } from "@flareapp/js";

if (process.env.NODE_ENV === 'production') {
    flare.light();
}
