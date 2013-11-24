package ladanse.website.servlet;

import javax.servlet.*;
import javax.servlet.annotation.WebFilter;
import javax.servlet.http.Cookie;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;

@WebFilter
public class PhpBBAuthenticationFilter implements Filter {

    private static String PHPBB3_COOKIE_K = "phpbb3_iouhm_k";
    private static String PHPBB3_COOKIE_SID = "phpbb3_iouhm_sid";
    private static String PHPBB3_COOKIE_U = "phpbb3_iouhm_u";

    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain) throws IOException, ServletException {

        HttpServletRequest httpRequest = (HttpServletRequest) request;
        HttpServletResponse httpResponse = (HttpServletResponse) response;

        Cookie[] cookies = httpRequest.getCookies();

        for(Cookie cookie : cookies)
        {
            System.err.println("COOKIE " + cookie.getName() + " " + cookie.getValue());
        }

        chain.doFilter(request, response);
    }

    @Override
    public void init(FilterConfig config) throws ServletException {
        // Nothing to do here!
    }

    @Override
    public void destroy() {
        // Nothing to do here!
    }
}
